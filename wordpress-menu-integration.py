#!/usr/bin/env python3
"""
Name: WordPress Menu Integration (Updated)
File: wordpress-menu-integration.py
Path: themes/youhealit/assets/scripts/wordpress-menu-integration.py
Function: Reads services from youhealit/includes/services-data.php and creates WordPress pages/menu items 
          so they match exactly what's defined in your existing services data
Usage:
    cd themes/youhealit/assets/scripts/
    python3 wordpress-menu-integration.py

Variables and Functions:
    env_loaded - boolean to confirm that the .env file data is loaded
    required_vars - array of required variables in the .env file ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASSWORD']
    db_connection - MySQL database connection object
    services_data - dictionary containing parsed service data from PHP file
    script_dir - Path object pointing to script directory location
    theme_dir - Path object pointing to WordPress theme root directory
    
    load_environment(self) - loads and validates environment variables from .env file
    connect_to_database(self) - establishes connection to remote WordPress database
    get_table_prefix(self) - auto-detects WordPress table prefix (wp_, wordpress_, etc.)
    read_services_data(self) - parses services from services-data.php file
    parse_service_content(self, content) - extracts individual service details from PHP array
    find_services_menu(self, table_prefix) - locates Services menu in WordPress database
    create_menu_items(self, menu_id, table_prefix) - creates WordPress menu items for each service
    cleanup(self) - properly closes database connections
    run(self) - main execution method that orchestrates the entire process

Dependencies: python-dotenv, mysql-connector-python
Requirements: .env file with database credentials, services-data.php in includes/ directory
"""

import os
import sys
import re
import mysql.connector
from mysql.connector import Error
from dotenv import load_dotenv
from pathlib import Path

class WordPressMenuIntegrator:
    def __init__(self):
        self.db_connection = None
        self.services_data = {}
        self.script_dir = Path(__file__).parent
        self.theme_dir = self.script_dir.parent.parent
        
    def load_environment(self):
        """Load environment variables from .env file"""
        print("🔧 Loading environment configuration...")
        
        # Look for .env file in multiple locations
        env_paths = [
            self.script_dir / '.env',
            self.theme_dir / '.env',
            Path.cwd() / '.env',
            Path.home() / '.env'
        ]
        
        env_loaded = False
        for env_path in env_paths:
            if env_path.exists():
                load_dotenv(env_path)
                print(f"✅ Loaded environment from: {env_path}")
                env_loaded = True
                break
        
        if not env_loaded:
            print("❌ No .env file found in expected locations:")
            for path in env_paths:
                print(f"   - {path}")
            return False
            
        # Verify required environment variables
        required_vars = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASSWORD']
        missing_vars = []
        
        for var in required_vars:
            if not os.getenv(var):
                missing_vars.append(var)
        
        if missing_vars:
            print(f"❌ Missing required environment variables: {', '.join(missing_vars)}")
            return False
            
        print("✅ All required environment variables found")
        return True
    
    def connect_to_database(self):
        """Connect to remote WordPress database"""
        print("🔌 Connecting to remote database...")
        
        try:
            config = {
                'host': os.getenv('DB_HOST'),
                'database': os.getenv('DB_NAME'),
                'user': os.getenv('DB_USER'),
                'password': os.getenv('DB_PASSWORD'),
                'port': int(os.getenv('DB_PORT', 3306)),
                'charset': 'utf8mb4',
                'autocommit': True,
                'connection_timeout': 30
            }
            
            # Add SSL configuration if needed (common for remote databases)
            if os.getenv('DB_SSL', 'false').lower() == 'true':
                config['ssl_disabled'] = False
                config['ssl_verify_cert'] = False
                config['ssl_verify_identity'] = False
            
            self.db_connection = mysql.connector.connect(**config)
            
            if self.db_connection.is_connected():
                db_info = self.db_connection.get_server_info()
                print(f"✅ Connected to MySQL Server version {db_info}")
                print(f"✅ Connected to database: {config['database']}")
                return True
                
        except Error as e:
            print(f"❌ Database connection failed: {e}")
            if "Access denied" in str(e):
                print("💡 Check your database credentials in .env file")
            elif "Can't connect" in str(e):
                print("💡 Check your DB_HOST and DB_PORT in .env file")
            elif "Unknown database" in str(e):
                print("💡 Check your DB_NAME in .env file")
            return False
    
    def get_table_prefix(self):
        """Get WordPress table prefix from wp_options table"""
        try:
            cursor = self.db_connection.cursor()
            
            # Try common prefixes
            common_prefixes = ['wp_', 'wordpress_', '']
            
            for prefix in common_prefixes:
                try:
                    cursor.execute(f"SELECT COUNT(*) FROM {prefix}options LIMIT 1")
                    cursor.fetchone()
                    print(f"✅ Found WordPress tables with prefix: '{prefix}'")
                    return prefix
                except Error:
                    continue
            
            print("❌ Could not determine WordPress table prefix")
            return None
            
        except Error as e:
            print(f"❌ Error determining table prefix: {e}")
            return None
        finally:
            if cursor:
                cursor.close()
    
    def read_services_data(self):
        """Read and parse services from services-data.php"""
        print("📖 Reading services data...")
        
        services_file = self.theme_dir / 'includes' / 'services-data.php'
        
        if not services_file.exists():
            print(f"❌ Services file not found: {services_file}")
            return False
        
        print(f"✅ Found services file: {services_file}")
        
        try:
            with open(services_file, 'r', encoding='utf-8') as f:
                content = f.read()
            
            # Use raw string for regex to avoid escape sequence warnings
            array_pattern = r'return\s*\[(.*?)\];'
            match = re.search(array_pattern, content, re.DOTALL)
            
            if not match:
                print("❌ Could not find services array in PHP file")
                return False
            
            array_content = match.group(1)
            
            # Parse individual service entries
            # Look for patterns like 'service-key' => [...],
            service_pattern = r"'([^']+)'\s*=>\s*\[(.*?)\],"
            services = re.findall(service_pattern, array_content, re.DOTALL)
            
            print(f"✅ Found {len(services)} services in PHP file")
            
            for service_key, service_content in services:
                # Parse service details
                self.services_data[service_key] = self.parse_service_content(service_content)
                print(f"   📋 {service_key}")
            
            return len(services) > 0
            
        except Exception as e:
            print(f"❌ Error reading services file: {e}")
            return False
    
    def parse_service_content(self, content):
        """Parse individual service content from PHP array"""
        service_data = {}
        
        # Common patterns for service data
        patterns = {
            'name': r"'name'\s*=>\s*'([^']*)'",
            'slug': r"'slug'\s*=>\s*'([^']*)'",
            'description': r"'description'\s*=>\s*'([^']*)'",
            'category': r"'category'\s*=>\s*'([^']*)'",
            'url': r"'url'\s*=>\s*'([^']*)'",
        }
        
        for key, pattern in patterns.items():
            match = re.search(pattern, content)
            if match:
                service_data[key] = match.group(1)
        
        return service_data
    
    def find_services_menu(self, table_prefix):
        """Find the Services menu in WordPress"""
        try:
            cursor = self.db_connection.cursor(dictionary=True)
            
            # Look for menus
            query = f"""
            SELECT t.term_id, t.name, t.slug 
            FROM {table_prefix}terms t
            JOIN {table_prefix}term_taxonomy tt ON t.term_id = tt.term_id
            WHERE tt.taxonomy = 'nav_menu'
            """
            
            cursor.execute(query)
            menus = cursor.fetchall()
            
            print(f"📋 Found {len(menus)} WordPress menus:")
            for menu in menus:
                print(f"   - {menu['name']} (ID: {menu['term_id']}, Slug: {menu['slug']})")
            
            # Look for Services menu
            services_menu = None
            for menu in menus:
                if 'services' in menu['name'].lower() or 'service' in menu['slug'].lower():
                    services_menu = menu
                    break
            
            if services_menu:
                print(f"✅ Found Services menu: {services_menu['name']} (ID: {services_menu['term_id']})")
                return services_menu['term_id']
            else:
                print("❌ No Services menu found")
                print("💡 Available menus:", [m['name'] for m in menus])
                return None
                
        except Error as e:
            print(f"❌ Error finding Services menu: {e}")
            return None
        finally:
            if cursor:
                cursor.close()
    
    def create_menu_items(self, menu_id, table_prefix):
        """Create menu items for services"""
        print(f"📝 Creating menu items for menu ID: {menu_id}")
        
        try:
            cursor = self.db_connection.cursor()
            
            # Get current max menu order
            cursor.execute(f"""
                SELECT COALESCE(MAX(menu_order), 0) as max_order 
                FROM {table_prefix}posts 
                WHERE post_type = 'nav_menu_item'
            """)
            max_order = cursor.fetchone()[0]
            
            created_count = 0
            
            for service_key, service_data in self.services_data.items():
                name = service_data.get('name', service_key.replace('-', ' ').title())
                url = service_data.get('url', f'/services/{service_key}/')
                
                # Check if menu item already exists
                cursor.execute(f"""
                    SELECT ID FROM {table_prefix}posts 
                    WHERE post_title = %s AND post_type = 'nav_menu_item'
                """, (name,))
                
                if cursor.fetchone():
                    print(f"   ⚠️  Menu item '{name}' already exists, skipping")
                    continue
                
                max_order += 1
                
                # Insert menu item post
                cursor.execute(f"""
                    INSERT INTO {table_prefix}posts 
                    (post_title, post_name, post_status, post_type, menu_order, post_date, post_date_gmt)
                    VALUES (%s, %s, 'publish', 'nav_menu_item', %s, NOW(), UTC_TIMESTAMP())
                """, (name, service_key, max_order))
                
                menu_item_id = cursor.lastrowid
                
                # Add menu item meta
                meta_data = [
                    ('_menu_item_type', 'custom'),
                    ('_menu_item_menu_item_parent', '0'),
                    ('_menu_item_object_id', str(menu_item_id)),
                    ('_menu_item_object', 'custom'),
                    ('_menu_item_target', ''),
                    ('_menu_item_classes', 'a:1:{i:0;s:0:"";}'),
                    ('_menu_item_xfn', ''),
                    ('_menu_item_url', url),
                ]
                
                for meta_key, meta_value in meta_data:
                    cursor.execute(f"""
                        INSERT INTO {table_prefix}postmeta (post_id, meta_key, meta_value)
                        VALUES (%s, %s, %s)
                    """, (menu_item_id, meta_key, meta_value))
                
                # Associate with menu
                cursor.execute(f"""
                    INSERT INTO {table_prefix}term_relationships (object_id, term_taxonomy_id)
                    VALUES (%s, %s)
                """, (menu_item_id, menu_id))
                
                print(f"   ✅ Created menu item: {name}")
                created_count += 1
            
            print(f"🎉 Successfully created {created_count} menu items!")
            return True
            
        except Error as e:
            print(f"❌ Error creating menu items: {e}")
            return False
        finally:
            if cursor:
                cursor.close()
    
    def cleanup(self):
        """Clean up database connection"""
        if self.db_connection and self.db_connection.is_connected():
            self.db_connection.close()
            print("🔌 Database connection closed")
    
    def run(self):
        """Main execution method"""
        print("🚀 WordPress Menu Integration v2 - Remote Database Edition")
        print("=" * 70)
        
        try:
            # Step 1: Load environment
            if not self.load_environment():
                return False
            
            # Step 2: Connect to database
            if not self.connect_to_database():
                return False
            
            # Step 3: Get table prefix
            table_prefix = self.get_table_prefix()
            if not table_prefix:
                return False
            
            # Step 4: Read services data
            if not self.read_services_data():
                return False
            
            # Step 5: Find Services menu
            menu_id = self.find_services_menu(table_prefix)
            if not menu_id:
                return False
            
            # Step 6: Create menu items
            if not self.create_menu_items(menu_id, table_prefix):
                return False
            
            print("\n🎉 Menu integration completed successfully!")
            return True
            
        except KeyboardInterrupt:
            print("\n⚠️ Operation cancelled by user")
            return False
        except Exception as e:
            print(f"\n❌ Unexpected error: {e}")
            return False
        finally:
            self.cleanup()

def main():
    """Entry point"""
    if len(sys.argv) > 1 and sys.argv[1] in ['-h', '--help']:
        print("""
WordPress Menu Integration v2

Usage: python3 wordpress-menu-integration.py

Requirements:
- .env file with database credentials (DB_HOST, DB_NAME, DB_USER, DB_PASSWORD)
- services-data.php file in theme/includes/ directory
- Existing 'Services' menu in WordPress

Environment variables:
- DB_HOST: Remote database host
- DB_NAME: WordPress database name  
- DB_USER: Database username
- DB_PASSWORD: Database password
- DB_PORT: Database port (default: 3306)
- DB_SSL: Enable SSL (default: false)
        """)
        return
    
    integrator = WordPressMenuIntegrator()
    success = integrator.run()
    sys.exit(0 if success else 1)

if __name__ == "__main__":
    main()