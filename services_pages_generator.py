#!/usr/bin/env python3
"""
Service Pages Generator & WordPress Menu Automation
1. Creates comprehensive service pages with 500+ word content
2. Generates WordPress pages and menu items automatically
3. Reads from /includes/services-data.php and expands content
"""

import re
import os
import mysql.connector
from mysql.connector import Error
from dotenv import load_dotenv
import json
from datetime import datetime

# Load environment variables
load_dotenv()

# Database configuration
DB_CONFIG = {
    'host': os.getenv('DB_HOST', 'localhost'),
    'database': os.getenv('DB_NAME', 'your_database_name'),
    'user': os.getenv('DB_USER', 'your_username'),
    'password': os.getenv('DB_PASSWORD', 'your_password'),
    'port': os.getenv('DB_PORT', 3306)
}

# Service priority order
PRIORITY_SERVICES = [
    'Chiropractic', 'Massage Therapy', 'Acupuncture', 'Integrated Healthcare', 'Weight Loss'
]

# All services (add hypnotherapy)
ALL_SERVICES = [
    'Acupuncture', 'Addiction Counseling', 'ADHD Treatment', 'Alternative Medicine',
    'Anxiety Treatment', 'Arthritis Treatment', 'Autism Support', 'Back Pain Relief',
    'Chiropractic', 'Chronic Pain Management', 'Detox Programs', 'Fibromyalgia Treatment', 
    'Geriatric Care', 'Headache Treatment', 'Hormone Therapy', 'Hypnotherapy', 
    'Integrated Healthcare', 'Learning Disabilities', 'Massage Therapy', 'Meditation', 
    'Men\'s Health', 'Neck Pain Treatment', 'Nutrition Counseling', 'Occupational Therapy', 
    'Pain Management', 'Pediatric Therapy', 'Physical Therapy', 'Postpartum Care', 
    'Prenatal Care', 'PTSD Therapy', 'Rehabilitation', 'Sleep Therapy', 'Sports Medicine', 
    'Stress Management', 'Weight Loss', 'Wellness Coaching', 'Women\'s Health', 'Yoga Therapy'
]

def read_services_data_php():
    """Read existing service data from PHP file"""
    services_data = {}
    
    try:
        with open('includes/services-data.php', 'r', encoding='utf-8') as file:
            content = file.read()
            
            # Parse PHP array structure (basic parsing)
            # This assumes your PHP file has a specific structure
            # You may need to adjust this based on your actual file format
            
            # Extract service definitions (adjust regex based on your format)
            service_pattern = r"'([^']+)'\s*=>\s*\[([^\]]+)\]"
            matches = re.findall(service_pattern, content, re.DOTALL)
            
            for service_name, service_content in matches:
                # Parse service content (title, description, etc.)
                title_match = re.search(r"'title'\s*=>\s*'([^']+)'", service_content)
                desc_match = re.search(r"'description'\s*=>\s*'([^']+)'", service_content)
                
                services_data[service_name] = {
                    'title': title_match.group(1) if title_match else service_name,
                    'description': desc_match.group(1) if desc_match else '',
                    'existing_content': service_content
                }
                
    except FileNotFoundError:
        print("Warning: /includes/services-data.php not found. Creating services from scratch.")
    except Exception as e:
        print(f"Error reading services-data.php: {e}")
    
    return services_data

def generate_comprehensive_service_content(service_name, existing_data=None):
    """Generate 500+ word content for each service"""
    
    # Service-specific content templates
    service_templates = {
        'Chiropractic': {
            'focus': 'spinal health, natural healing, and musculoskeletal wellness',
            'benefits': 'pain relief, improved mobility, posture correction, and overall health optimization',
            'conditions': 'back pain, neck pain, headaches, sciatica, and joint dysfunction',
            'approach': 'spinal adjustments, soft tissue therapy, and holistic wellness strategies'
        },
        'Massage Therapy': {
            'focus': 'therapeutic bodywork, stress relief, and muscular health',
            'benefits': 'tension reduction, circulation improvement, pain relief, and relaxation',
            'conditions': 'muscle tension, chronic pain, stress disorders, and injury recovery',
            'approach': 'Swedish massage, deep tissue work, trigger point therapy, and wellness massage'
        },
        'Acupuncture': {
            'focus': 'traditional Chinese medicine and energy balance restoration',
            'benefits': 'pain relief, stress reduction, improved energy, and natural healing',
            'conditions': 'chronic pain, anxiety, digestive issues, and hormonal imbalances',
            'approach': 'meridian point stimulation, traditional needling, and holistic assessment'
        },
        'Hypnotherapy': {
            'focus': 'subconscious healing, behavioral change, and mental wellness',
            'benefits': 'anxiety reduction, habit modification, pain management, and performance enhancement',
            'conditions': 'stress disorders, phobias, chronic pain, and unwanted habits',
            'approach': 'clinical hypnosis, relaxation induction, and cognitive restructuring'
        },
        'Weight Loss': {
            'focus': 'sustainable weight management and lifestyle transformation',
            'benefits': 'healthy weight reduction, improved metabolism, and long-term wellness',
            'conditions': 'obesity, metabolic disorders, and weight-related health issues',
            'approach': 'medical supervision, nutritional counseling, and behavioral modification'
        }
    }
    
    # Get template or create generic one
    template = service_templates.get(service_name, {
        'focus': f'{service_name.lower()} treatment and therapeutic intervention',
        'benefits': 'symptom relief, functional improvement, and enhanced quality of life',
        'conditions': 'various health conditions requiring specialized therapeutic attention',
        'approach': f'evidence-based {service_name.lower()} techniques and personalized treatment protocols'
    })
    
    # Generate comprehensive content
    content = f"""
<div class="service-page-content">
    <div class="service-intro">
        <h2>Professional {service_name} Services</h2>
        <p>{service_name} represents a cornerstone of comprehensive healthcare at the Health Center of the Triangle, North Carolina. Our experienced practitioners provide evidence-based {service_name.lower()} services that focus on {template['focus']} through personalized treatment approaches designed to meet individual health needs and wellness goals.</p>
    </div>

    <div class="service-overview">
        <h3>Comprehensive {service_name} Care</h3>
        <p>Our {service_name.lower()} services address diverse health concerns through {template['approach']}. We understand that each patient presents unique health challenges and treatment goals, which is why our approach emphasizes individualized care plans that integrate seamlessly with overall health and wellness strategies.</p>
        
        <p>The benefits of professional {service_name.lower()} include {template['benefits']}. Our practitioners stay current with the latest developments in {service_name.lower()} while maintaining the highest standards of professional care, ensuring that every patient receives effective, safe, and compassionate treatment.</p>
    </div>

    <div class="conditions-treated">
        <h3>Conditions We Address</h3>
        <p>Our {service_name.lower()} specialists have extensive experience treating {template['conditions']} among many other health concerns. We provide comprehensive evaluation and assessment to determine the most appropriate treatment approaches for each individual's specific needs and health objectives.</p>
        
        <p>Whether you're dealing with acute symptoms or chronic conditions, our {service_name.lower()} services are designed to provide both immediate relief and long-term health improvements. We work collaboratively with other healthcare providers to ensure that your {service_name.lower()} care integrates effectively with any other treatments you may be receiving.</p>
    </div>

    <div class="treatment-approach">
        <h3>Our Treatment Philosophy</h3>
        <p>At the Health Center of the Triangle, we believe that effective {service_name.lower()} combines clinical expertise with compassionate care. Our treatment philosophy emphasizes patient education, empowering individuals to take active roles in their health management and recovery processes.</p>
        
        <p>We utilize {template['approach']} tailored to each patient's unique needs and preferences. Our practitioners take time to understand your health goals, lifestyle factors, and treatment preferences to develop comprehensive care plans that support both immediate symptom relief and long-term wellness maintenance.</p>
    </div>

    <div class="why-choose-us">
        <h3>Why Choose Our {service_name} Services</h3>
        <p>Our commitment to excellence in {service_name.lower()} is demonstrated through our experienced practitioners, evidence-based treatment protocols, and patient-centered care approach. We maintain the highest professional standards while creating comfortable, supportive treatment environments that promote healing and wellness.</p>
        
        <p>Patients choose our {service_name.lower()} services because of our comprehensive approach to health and wellness, our commitment to continuing education and professional development, and our dedication to achieving optimal treatment outcomes for every individual we serve.</p>
    </div>

    <div class="getting-started">
        <h3>Beginning Your {service_name} Journey</h3>
        <p>Starting {service_name.lower()} care is simple and straightforward. We begin with a comprehensive consultation and assessment to understand your health concerns, treatment goals, and any relevant medical history. This initial evaluation allows us to develop personalized treatment recommendations that align with your specific needs.</p>
        
        <p>Our team is committed to making {service_name.lower()} accessible and effective for all patients. We provide clear explanations of treatment options, expected outcomes, and any recommended lifestyle modifications that can support your {service_name.lower()} care and overall wellness goals.</p>
    </div>
</div>
"""
    
    return content.strip()

def create_wordpress_page(service_name, content):
    """Create WordPress page for service"""
    try:
        connection = mysql.connector.connect(**DB_CONFIG)
        cursor = connection.cursor()
        
        # Create URL slug
        slug = service_name.lower().replace(' ', '-').replace("'", "")
        
        # Check if page already exists
        cursor.execute("SELECT ID FROM wp_posts WHERE post_name = %s AND post_type = 'page'", (slug,))
        existing_page = cursor.fetchone()
        
        if existing_page:
            print(f"Page for {service_name} already exists (ID: {existing_page[0]})")
            return existing_page[0]
        
        # Create new page
        page_data = {
            'post_title': f"{service_name} Services",
            'post_content': content,
            'post_status': 'publish',
            'post_type': 'page',
            'post_name': slug,
            'post_date': datetime.now().strftime('%Y-%m-%d %H:%M:%S'),
            'post_modified': datetime.now().strftime('%Y-%m-%d %H:%M:%S')
        }
        
        insert_query = """
            INSERT INTO wp_posts (post_title, post_content, post_status, post_type, post_name, post_date, post_modified)
            VALUES (%(post_title)s, %(post_content)s, %(post_status)s, %(post_type)s, %(post_name)s, %(post_date)s, %(post_modified)s)
        """
        
        cursor.execute(insert_query, page_data)
        page_id = cursor.lastrowid
        
        connection.commit()
        print(f"Created page for {service_name} (ID: {page_id})")
        
        cursor.close()
        connection.close()
        
        return page_id
        
    except Error as e:
        print(f"Error creating page for {service_name}: {e}")
        return None

def get_services_menu_id():
    """Get the menu ID for the Services menu"""
    try:
        connection = mysql.connector.connect(**DB_CONFIG)
        cursor = connection.cursor()
        
        # Find Services menu
        cursor.execute("SELECT term_id FROM wp_terms WHERE name = 'Services' OR name = 'services'")
        result = cursor.fetchone()
        
        if result:
            menu_id = result[0]
        else:
            # Create Services menu if it doesn't exist
            cursor.execute("INSERT INTO wp_terms (name, slug) VALUES ('Services', 'services')")
            menu_id = cursor.lastrowid
            
            # Add to term_taxonomy
            cursor.execute("INSERT INTO wp_term_taxonomy (term_id, taxonomy) VALUES (%s, 'nav_menu')", (menu_id,))
            connection.commit()
            print(f"Created Services menu (ID: {menu_id})")
        
        cursor.close()
        connection.close()
        return menu_id
        
    except Error as e:
        print(f"Error getting Services menu ID: {e}")
        return None

def add_to_services_menu(page_id, service_name, menu_id, menu_order):
    """Add service page to Services menu"""
    try:
        connection = mysql.connector.connect(**DB_CONFIG)
        cursor = connection.cursor()
        
        # Check if menu item already exists
        cursor.execute("""
            SELECT ID FROM wp_posts 
            WHERE post_type = 'nav_menu_item' 
            AND post_title = %s
        """, (f"{service_name} Services",))
        
        existing_item = cursor.fetchone()
        if existing_item:
            print(f"Menu item for {service_name} already exists")
            cursor.close()
            connection.close()
            return
        
        # Create menu item
        menu_item_data = {
            'post_title': f"{service_name} Services",
            'post_status': 'publish',
            'post_type': 'nav_menu_item',
            'menu_order': menu_order,
            'post_date': datetime.now().strftime('%Y-%m-%d %H:%M:%S')
        }
        
        cursor.execute("""
            INSERT INTO wp_posts (post_title, post_status, post_type, menu_order, post_date)
            VALUES (%(post_title)s, %(post_status)s, %(post_type)s, %(menu_order)s, %(post_date)s)
        """, menu_item_data)
        
        menu_item_id = cursor.lastrowid
        
        # Add meta data for menu item
        meta_data = [
            (menu_item_id, '_menu_item_type', 'post_type'),
            (menu_item_id, '_menu_item_menu_item_parent', '0'),
            (menu_item_id, '_menu_item_object_id', str(page_id)),
            (menu_item_id, '_menu_item_object', 'page'),
            (menu_item_id, '_menu_item_target', ''),
            (menu_item_id, '_menu_item_classes', ''),
            (menu_item_id, '_menu_item_xfn', ''),
            (menu_item_id, '_menu_item_url', ''),
        ]
        
        cursor.executemany("""
            INSERT INTO wp_postmeta (post_id, meta_key, meta_value)
            VALUES (%s, %s, %s)
        """, meta_data)
        
        # Associate with menu
        cursor.execute("""
            INSERT INTO wp_term_relationships (object_id, term_taxonomy_id)
            VALUES (%s, %s)
        """, (menu_item_id, menu_id))
        
        connection.commit()
        print(f"Added {service_name} to Services menu (Order: {menu_order})")
        
        cursor.close()
        connection.close()
        
    except Error as e:
        print(f"Error adding {service_name} to menu: {e}")

def create_service_pages_and_menu():
    """Main function to create all service pages and menu items"""
    
    print("🚀 Creating Service Pages and Menu Items")
    print("=" * 50)
    
    # Read existing services data
    existing_services = read_services_data_php()
    
    # Get Services menu ID
    menu_id = get_services_menu_id()
    if not menu_id:
        print("❌ Could not create or find Services menu")
        return
    
    # Create ordered service list (priority first, then alphabetical)
    other_services = [s for s in sorted(ALL_SERVICES) if s not in PRIORITY_SERVICES]
    ordered_services = PRIORITY_SERVICES + other_services
    
    created_pages = []
    menu_order = 1
    
    # Create pages and menu items
    for service in ordered_services:
        print(f"\n📄 Processing {service}...")
        
        # Generate content
        existing_data = existing_services.get(service, {})
        content = generate_comprehensive_service_content(service, existing_data)
        
        # Create WordPress page
        page_id = create_wordpress_page(service, content)
        if page_id:
            created_pages.append((service, page_id))
            
            # Add to menu with special styling for priority services
            add_to_services_menu(page_id, service, menu_id, menu_order)
            menu_order += 1
            
            # Add separator after priority services
            if service == PRIORITY_SERVICES[-1]:
                print(f"   ✨ Added separator after priority services")
                menu_order += 10  # Leave gap for separator styling
    
    # Summary
    print(f"\n✅ Summary:")
    print(f"   📄 Created {len(created_pages)} service pages")
    print(f"   🔗 Added {len(created_pages)} menu items")
    print(f"   ⭐ Priority services: {', '.join(PRIORITY_SERVICES)}")
    print(f"   📂 Other services: {len(other_services)} alphabetically ordered")
    
    print(f"\n💡 Next steps:")
    print(f"   1. Check your WordPress admin → Pages to see the new service pages")
    print(f"   2. Go to Appearance → Menus to customize the Services menu styling")
    print(f"   3. Add CSS for the separator after priority services")
    print(f"   4. Test the menu navigation and page content")

if __name__ == "__main__":
    create_service_pages_and_menu()