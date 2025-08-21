#!/usr/bin/env python3
"""
Services Preview Script
File: services-preview.py
Path: themes/youhealit/assets/scripts/services-preview.py

Reads and lists ALL services from youhealit/includes/services-data.php
Shows exactly what service pages and menu items will be created

Usage: 
    cd themes/youhealit/assets/scripts/
    python3 services-preview.py
"""

import os
import re

def read_and_count_services():
    """Read and count all services from services-data.php"""
    
    # Path to services-data.php from scripts directory
    services_file = os.path.join(os.path.dirname(__file__), '..', '..', 'includes', 'services-data.php')
    
    try:
        with open(services_file, 'r', encoding='utf-8') as file:
            content = file.read()
            
            print(f"✅ Found services-data.php at: {services_file}")
            print(f"📄 File size: {len(content)} characters")
            
            # Show first 500 characters to understand structure
            print(f"\n📖 File preview (first 500 chars):")
            print("-" * 50)
            print(content[:500] + "..." if len(content) > 500 else content)
            print("-" * 50)
            
            services_data = {}
            
            # Method 1: Try to find array structure like $services = array(...) or $services = [...]
            patterns = [
                r"\$services\s*=\s*array\s*\((.*?)\);",  # $services = array(...);
                r"\$services\s*=\s*\[(.*?)\];",          # $services = [...];
                r"return\s*array\s*\((.*?)\);",          # return array(...);
                r"return\s*\[(.*?)\];",                  # return [...];
                r"\$[a-zA-Z_]+\s*=\s*array\s*\((.*?)\);", # any variable = array(...);
                r"\$[a-zA-Z_]+\s*=\s*\[(.*?)\];",        # any variable = [...];
            ]
            
            array_content = None
            used_pattern = None
            for pattern in patterns:
                match = re.search(pattern, content, re.DOTALL | re.IGNORECASE)
                if match:
                    array_content = match.group(1)
                    used_pattern = pattern
                    print(f"\n✅ Found services array using pattern: {pattern}")
                    break
            
            if array_content:
                print(f"\n📋 Extracted array content (first 300 chars):")
                print("-" * 30)
                print(array_content[:300] + "..." if len(array_content) > 300 else array_content)
                print("-" * 30)
                
                # Method 2: Extract service keys from array content
                # Look for patterns like 'service-name' => array(...) or "service-name" => [...]
                service_patterns = [
                    r"['\"]([^'\"]+)['\"\s]*=>",          # 'key' => or "key" =>
                    r"['\"]([a-z-]+)['\"]",               # Just 'key' or "key"
                ]
                
                all_matches = set()
                for service_pattern in service_patterns:
                    matches = re.findall(service_pattern, array_content)
                    for match in matches:
                        if len(match) > 2 and '-' in match:  # Likely a service key
                            all_matches.add(match)
                
                # Convert to proper service names
                for service_key in sorted(all_matches):
                    service_name = service_key.replace('-', ' ').replace('_', ' ').title()
                    services_data[service_name] = {
                        'key': service_key,
                        'name': service_name,
                        'url': f"/{service_key}/"
                    }
                
                print(f"\n✅ Extracted {len(services_data)} services from PHP file")
                
            else:
                print("\n⚠️  Could not find services array - trying alternative methods...")
                
                # Alternative: look for individual service definitions or menu items
                alternative_patterns = [
                    r"menu-item-[0-9]+['\"][^>]*href=['\"][^'\"]*\/([a-z-]+)\/['\"]",  # menu links
                    r"<a[^>]*href=['\"][^'\"]*\/([a-z-]+)\/['\"]",                     # any links
                    r"['\"]([a-z-]+(?:-[a-z]+)*)['\"]",                                # quoted strings with hyphens
                ]
                
                potential_services = set()
                for alt_pattern in alternative_patterns:
                    matches = re.findall(alt_pattern, content, re.IGNORECASE)
                    for match in matches:
                        if len(match) > 3 and match not in ['home', 'about', 'contact', 'page']:
                            potential_services.add(match)
                
                for service_key in sorted(potential_services):
                    service_name = service_key.replace('-', ' ').replace('_', ' ').title()
                    services_data[service_name] = {
                        'key': service_key,
                        'name': service_name,
                        'url': f"/{service_key}/"
                    }
                
                print(f"⚠️  Alternative extraction found {len(services_data)} potential services")
            
            # Display results
            if services_data:
                print(f"\n🎯 SERVICES THAT WILL BE CREATED:")
                print("=" * 60)
                
                for i, (name, data) in enumerate(services_data.items(), 1):
                    print(f"{i:2d}. {name}")
                    print(f"    📄 WordPress Page: {data['name']} Services")
                    print(f"    🔗 URL: {data['url']}")
                    print(f"    🎛️  Menu: Services → {name}")
                    print()
                
                print(f"📊 TOTAL: {len(services_data)} service pages and menu items will be created")
                
                # Breakdown by type
                priority_services = ['Chiropractic', 'Massage Therapy', 'Acupuncture', 'Integrated Healthcare', 'Weight Loss']
                priority_found = [name for name in services_data.keys() if any(p in name for p in priority_services)]
                other_services = [name for name in services_data.keys() if name not in priority_found]
                
                print(f"\n📋 SERVICE BREAKDOWN:")
                print(f"   ⭐ Priority services: {len(priority_found)}")
                for service in priority_found:
                    print(f"      • {service}")
                
                print(f"   📂 Other services: {len(other_services)} (alphabetical)")
                for service in sorted(other_services)[:5]:  # Show first 5
                    print(f"      • {service}")
                if len(other_services) > 5:
                    print(f"      ... and {len(other_services) - 5} more")
                
                return services_data
            else:
                print("\n❌ No services found in the file")
                print("💡 Please check if the file contains service definitions")
                return {}
        
    except FileNotFoundError:
        print(f"❌ Could not find services-data.php at: {services_file}")
        print("💡 Please check the file path:")
        print(f"   Expected: themes/youhealit/includes/services-data.php")
        print(f"   Actual: {services_file}")
        return {}
    except Exception as e:
        print(f"❌ Error reading services-data.php: {e}")
        return {}

def main():
    """Main preview function"""
    print("🔍 Services Preview - Analyzing services-data.php")
    print("=" * 60)
    
    services = read_and_count_services()
    
    if services:
        print(f"\n🚀 READY TO CREATE:")
        print(f"   📄 {len(services)} WordPress Pages")
        print(f"   🔗 {len(services)} Menu Items under Services")
        print(f"   📊 Total: {len(services)} service pages")
        
        print(f"\n💡 To create these pages and menu items, run:")
        print(f"   python3 wordpress-menu-integration-updated.py")
    else:
        print(f"\n❌ No services found - cannot create pages")
        print(f"💡 Please check your services-data.php file structure")

if __name__ == "__main__":
    main()
