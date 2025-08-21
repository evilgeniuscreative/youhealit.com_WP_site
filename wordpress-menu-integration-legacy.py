#!/usr/bin/env python3
"""
Name: WordPress Menu Integration (Legacy Compatible)
File: wordpress-menu-integration-legacy.py
Path: themes/youhealit/assets/scripts/wordpress-menu-integration-legacy.py
Function: Reads services from youhealit/includes/services-data.php and creates WordPress pages/menu items 
          so they match exactly what's defined in your existing services data
          Compatible with Python 3.6+
Usage:
    cd themes/youhealit/assets/scripts/
    python3 wordpress-menu-integration-legacy.py

Variables and Functions:
    env_loaded - boolean to confirm that the .env file data is loaded
    required_vars - list of required variables in the .env file ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASSWORD']
    db_connection - MySQL database connection object
    services_data - dictionary containing parsed service data from PHP file
    script_dir - string path to script directory location
    theme_dir - string path to WordPress theme root directory
    
    load_environment(self) - loads and validates environment variables from .env file
    connect_to_database(self) - establishes connection to remote WordPress database
    get_table_prefix(self) - auto-detects WordPress table prefix (wp_, wordpress_, etc.)
    read_services_data(self) - parses services from services-data.php file
    parse_service_content(self, content) - extracts individual service details from PHP array
    find_services_menu(self, table_prefix) - locates Services menu in WordPress database
    create_menu_items(self, menu_id, table_prefix) - creates WordPress menu items for each service
    cleanup(self) - properly closes database connections
    run(self) - main execution method that orchestrates the entire process

Dependencies: mysql-connector-python, python-dotenv (install with: pip install mysql-connector-python python-dotenv)
Requirements: .env file with database credentials, services-data.php in includes/ directory
Python Version: Compatible with Python 3.6+
"""

import os
import sys
import re
import mysql.connector
from mysql.connector import Error

# Import dotenv with fallback for older systems
try:
    from dotenv import load_dotenv
except ImportError:
    print("Warning: python-dotenv not available. Please set environment variables manually.")
    def load_dotenv(path=None):
        return False

class WordPressMenuIntegrator:
    def __init__(self):
        self.db_connection = None
        self.services_data = {}
        # Use os.path instead of pathlib for Python 3.6 compatibility
        self.script_dir = os.path.dirname(os.path.abspath(__file__))
        self.theme_dir = os.path.dirname(os.path.dirname(self.script_dir))
        
    def load_environment(self):
        """Load environment variables from .env file"""
        print("🔧 Loading environment configuration...")
        
        # Look for .env file in multiple locations
        env_paths = [
            os.path.join(self.script_dir, '.env'),
            os.path.join(self.theme_dir, '.env'),
            os.path.join(os.getcwd(), '.env'),
            os.path.join(os.path.expanduser('~'), '.env')
        ]
        
        env_loaded = False
        for env_path in env_paths:
            if os.path.exists(env_path):
                load_dotenv(env_path)
                print("✅ Loaded environment from: {}".format(env_path))
                env_loaded = True
                break
        
        if not env_loaded:
            print("❌ No .env file found in expected locations:")
            for path in env_paths:
                print("   - {}".format(path))
            return False
            
        # Verify required environment variables
        required_vars = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASSWORD']
        missing_vars = []
        
        for var in required_vars:
            if not os.getenv(var):
                missing_vars.append(var)
        
        if missing_vars:
            print("❌ Missing required environment variables: {}".format(', '.join(missing_vars)))
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
                print("✅ Connected to MySQL Server version {}".format(db_info))
                print("✅ Connected to database: {}".format(config['database']))
                return True
                
        except Error as e:
            print("❌ Database connection failed: {}".format(e))
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
                    cursor.execute("SELECT COUNT(*) FROM {}options LIMIT 1".format(prefix))
                    cursor.fetchone()
                    print("✅ Found WordPress tables with prefix: '{}'".format(prefix))
                    return prefix
                except Error:
                    continue
            
            print("❌ Could not determine WordPress table prefix")
            return None
            
        except Error as e:
            print("❌ Error determining table prefix: {}".format(e))
            return None
        finally:
            if cursor:
                cursor.close()
    
    def read_services_data(self):
        """Read and parse services from services-data.php"""
        print("📖 Reading services data...")
        
        services_file = os.path.join(self.theme_dir, 'includes', 'services-data.php')
        
        if not os.path.exists(services_file):
            print("❌ Services file not found: {}".format(services_file))
            return False
        
        print("✅ Found services file: {}".format(services_file))
        
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
            
            print("✅ Found {} services in PHP file".format(len(services)))
            
            for service_key, service_content in services:
                # Parse service details
                self.services_data[service_key] = self.parse_service_content(service_content)
                print("   📋 {}".format(service_key))
            
            return len(services) > 0
            
        except Exception as e:
            print("❌ Error reading services file: {}".format(e))
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
            query = """
            SELECT t.term_id, t.name, t.slug 
            FROM {}terms t
            JOIN {}term_taxonomy tt ON t.term_id = tt.term_id
            WHERE tt.taxonomy = 'nav_menu'
            """.format(table_prefix, table_prefix)
            
            cursor.execute(query)
            menus = cursor.fetchall()
            
            print("📋 Found {} WordPress menus:".format(len(menus)))
            for menu in menus:
                print("   - {} (ID: {}, Slug: {})".format(menu['name'], menu['term_id'], menu['slug']))
            
            # Look for Services menu
            services_menu = None
            for menu in menus:
                if 'services' in menu['name'].lower() or 'service' in menu['slug'].lower():
                    services_menu = menu
                    break
            
            if services_menu:
                print("✅ Found Services menu: {} (ID: {})".format(services_menu['name'], services_menu['term_id']))
                return services_menu['term_id']
            else:
                print("❌ No Services menu found")
                print("💡 Available menus: {}".format([m['name'] for m in menus]))
                return None
                
        except Error as e:
            print("❌ Error finding Services menu: {}".format(e))
            return None
        finally:
            if cursor:
                cursor.close()
    
    def create_menu_items(self, menu_id, table_prefix):
        """Create menu items for services"""
        print("📝 Creating menu items for menu ID: {}".format(menu_id))
        
        try:
            cursor = self.db_connection.cursor()
            
            # Get current max menu order
            cursor.execute("""
                SELECT COALESCE(MAX(menu_order), 0) as max_order 
                FROM {}posts 
                WHERE post_type = 'nav_menu_item'
            """.format(table_prefix))
            result = cursor.fetchone()
            max_order = result[0] if result else 0
            
            created_count = 0
            
            for service_key, service_data in self.services_data.items():
                name = service_data.get('name', service_key.replace('-', ' ').title())
                url = service_data.get('url', '/services/{}/'.format(service_key))
                
                # Check if menu item already exists
                cursor.execute("""
                    SELECT ID FROM {}posts 
                    WHERE post_title = %s AND post_type = 'nav_menu_item'
                """.format(table_prefix), (name,))
                
                if cursor.fetchone():
                    print("   ⚠️  Menu item '{}' already exists, skipping".format(name))
                    continue
                
                max_order += 1
                
                # Insert menu item post
                cursor.execute("""
                    INSERT INTO {}posts 
                    (post_title, post_name, post_status, post_type, menu_order, post_date, post_date_gmt)
                    VALUES (%s, %s, 'publish', 'nav_menu_item', %s, NOW(), UTC_TIMESTAMP())
                """.format(table_prefix), (name, service_key, max_order))
                
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
                    cursor.execute("""
                        INSERT INTO {}postmeta (post_id, meta_key, meta_value)
                        VALUES (%s, %s, %s)
                    """.format(table_prefix), (menu_item_id, meta_key, meta_value))
                
                # Associate with menu
                cursor.execute("""
                    INSERT INTO {}term_relationships (object_id, term_taxonomy_id)
                    VALUES (%s, %s)
                """.format(table_prefix), (menu_item_id, menu_id))
                
                print("   ✅ Created menu item: {}".format(name))
                created_count += 1
            
            print("🎉 Successfully created {} menu items!".format(created_count))
            return True
            
        except Error as e:
            print("❌ Error creating menu items: {}".format(e))
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
        print("🚀 WordPress Menu Integration v2 - Legacy Compatible Edition")
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
            print("\n❌ Unexpected error: {}".format(e))
            return False
        finally:
            self.cleanup()

def main():
    """Entry point"""
    if len(sys.argv) > 1 and sys.argv[1] in ['-h', '--help']:
        print("""
WordPress Menu Integration v2 - Legacy Compatible

Usage: python3 wordpress-menu-integration-legacy.py

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

Compatible with Python 3.6+
        """)
        return
    
    integrator = WordPressMenuIntegrator()
    success = integrator.run()
    sys.exit(0 if success else 1)

if __name__ == "__main__":
    main()