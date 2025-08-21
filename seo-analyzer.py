#!/usr/bin/env python3
"""
Complete SEO Opportunity Analyzer for YouHealIt Service + Location Pages
Analyzes 3,979 locations to identify highest-value opportunities for 45 services
"""

import pandas as pd
import numpy as np
from collections import defaultdict, Counter
import json

# Configuration
CSV_FILE = "big.csv"
OUTPUT_FILE = "seo_opportunities.csv"

# Your 45 services - prioritized by likely search volume
SERVICES = [
    # Tier 1: High-volume primary services (Top 15)
    "Chiropractic", "Physical Therapy", "Massage Therapy", "Mental Health Counseling",
    "Pain Management", "Acupuncture", "Weight Loss", "Sports Medicine", 
    "Occupational Therapy", "Nutrition Counseling", "Speech Therapy",
    "Addiction Counseling", "Anxiety Treatment", "Depression Counseling", "Back Pain Relief",
    
    # Tier 2: Medium-volume specialized services (Next 15)
    "Wellness Coaching", "Yoga Therapy", "Stress Management", "Sleep Therapy",
    "Family Therapy", "Couples Therapy", "Chronic Pain Management", "Women's Health",
    "Men's Health", "Prenatal Care", "Hormone Therapy", "Rehabilitation",
    "Arthritis Treatment", "Neck Pain Treatment", "Headache Treatment",
    
    # Tier 3: Lower-volume niche services (Final 15)
    "Meditation", "Group Therapy", "Pediatric Therapy", "Geriatric Care",
    "Postpartum Care", "Fertility Support", "Detox Programs", "Fibromyalgia Treatment",
    "PTSD Therapy", "ADHD Treatment", "Autism Support", "Learning Disabilities",
    "Cognitive Therapy", "Behavioral Therapy", "Alternative Medicine"
]

def analyze_location_data():
    """Comprehensive analysis of location data"""
    
    print("🔍 Loading and analyzing location data...")
    
    try:
        df = pd.read_csv(CSV_FILE)
        print(f"✅ Loaded {len(df):,} locations")
    except Exception as e:
        print(f"❌ Error loading CSV: {e}")
        return None
    
    # Basic statistics
    print(f"\n📊 DATASET OVERVIEW:")
    print(f"   Total locations: {len(df):,}")
    print(f"   Unique cities: {df['city_name'].nunique():,}")
    print(f"   Unique neighborhoods: {df['city_section_name'].nunique():,}")
    
    # Analyze city distribution
    city_counts = df['city_name'].value_counts()
    
    print(f"\n🏙️ TOP 25 CITIES BY LOCATION COUNT:")
    print("="*50)
    for i, (city, count) in enumerate(city_counts.head(25).items(), 1):
        print(f"{i:2d}. {city:<30} {count:4d} locations")
    
    return df, city_counts

def categorize_cities(city_counts):
    """Categorize cities into tiers for SEO strategy"""
    
    tiers = {
        'tier1': [],  # 100+ locations (Major metropolitan areas)
        'tier2': [],  # 50-99 locations (Large cities)  
        'tier3': [],  # 20-49 locations (Medium cities)
        'tier4': [],  # 10-19 locations (Small cities)
        'tier5': [],  # 5-9 locations (Towns)
        'tier6': []   # 1-4 locations (Small towns)
    }
    
    for city, count in city_counts.items():
        if count >= 100:
            tiers['tier1'].append((city, count))
        elif count >= 50:
            tiers['tier2'].append((city, count))
        elif count >= 20:
            tiers['tier3'].append((city, count))
        elif count >= 10:
            tiers['tier4'].append((city, count))
        elif count >= 5:
            tiers['tier5'].append((city, count))
        else:
            tiers['tier6'].append((city, count))
    
    print(f"\n🎯 CITY TIER ANALYSIS:")
    print("="*50)
    
    tier_names = {
        'tier1': 'Tier 1 (Major Metro, 100+ locations)',
        'tier2': 'Tier 2 (Large Cities, 50-99 locations)', 
        'tier3': 'Tier 3 (Medium Cities, 20-49 locations)',
        'tier4': 'Tier 4 (Small Cities, 10-19 locations)',
        'tier5': 'Tier 5 (Towns, 5-9 locations)',
        'tier6': 'Tier 6 (Small Towns, 1-4 locations)'
    }
    
    for tier_key, tier_name in tier_names.items():
        cities = tiers[tier_key]
        print(f"\n{tier_name}: {len(cities)} cities")
        
        if len(cities) <= 8:  # Show all if 8 or fewer
            for city, count in cities:
                print(f"   • {city} ({count} locations)")
        else:  # Show top 8 for larger lists
            for city, count in cities[:8]:
                print(f"   • {city} ({count} locations)")
            print(f"   ... and {len(cities)-8} more")
    
    return tiers

def calculate_seo_strategy(tiers, services):
    """Calculate optimal SEO strategy by tier"""
    
    strategies = []
    total_pages = 0
    
    # Strategy for each tier
    tier_strategies = {
        'tier1': {
            'name': 'Tier 1 (Major Metro)',
            'services': len(services),  # All 45 services
            'description': 'All services - highest competition but highest reward'
        },
        'tier2': {
            'name': 'Tier 2 (Large Cities)', 
            'services': 30,  # Top 30 services
            'description': 'Top 30 services - good search volume, manageable competition'
        },
        'tier3': {
            'name': 'Tier 3 (Medium Cities)',
            'services': 20,  # Top 20 services
            'description': 'Top 20 services - moderate search volume'
        },
        'tier4': {
            'name': 'Tier 4 (Small Cities)',
            'services': 15,  # Top 15 services
            'description': 'Top 15 services - lower competition'
        },
        'tier5': {
            'name': 'Tier 5 (Towns)',
            'services': 10,  # Top 10 services
            'description': 'Top 10 services - easy to rank'
        },
        'tier6': {
            'name': 'Tier 6 (Small Towns)',
            'services': 5,   # Top 5 services only
            'description': 'Top 5 essential services only'
        }
    }
    
    print(f"\n📈 RECOMMENDED SEO STRATEGY:")
    print("="*60)
    
    for tier_key, strategy in tier_strategies.items():
        cities = tiers[tier_key]
        num_cities = len(cities)
        num_services = strategy['services']
        pages_for_tier = num_cities * num_services
        total_pages += pages_for_tier
        
        strategies.append({
            'tier': strategy['name'],
            'cities': num_cities,
            'services': num_services,
            'pages': pages_for_tier,
            'description': strategy['description']
        })
        
        print(f"\n{strategy['name']}:")
        print(f"   Cities: {num_cities}")
        print(f"   Services: {num_services}")
        print(f"   Pages: {pages_for_tier:,}")
        print(f"   Strategy: {strategy['description']}")
    
    print(f"\n🎯 TOTAL STRATEGIC PAGES: {total_pages:,}")
    print(f"   (Down from potential {len(tiers['tier1']) + len(tiers['tier2']) + len(tiers['tier3']) + len(tiers['tier4']) + len(tiers['tier5']) + len(tiers['tier6'])} cities × 45 services = {(len(tiers['tier1']) + len(tiers['tier2']) + len(tiers['tier3']) + len(tiers['tier4']) + len(tiers['tier5']) + len(tiers['tier6'])) * 45:,} pages)")
    
    return strategies, total_pages

def generate_priority_combinations(tiers, services):
    """Generate prioritized list of city/service combinations"""
    
    priority_combinations = []
    
    # Tier 1: All services for major metros
    for city, count in tiers['tier1']:
        for i, service in enumerate(services):
            priority_combinations.append({
                'city': city,
                'service': service,
                'tier': 1,
                'priority_score': 1000 + count + (45 - i),  # High base + location count + service priority
                'estimated_competition': 'Very High',
                'estimated_volume': 'Very High'
            })
    
    # Tier 2: Top 30 services for large cities
    for city, count in tiers['tier2']:
        for i, service in enumerate(services[:30]):
            priority_combinations.append({
                'city': city,
                'service': service,
                'tier': 2,
                'priority_score': 800 + count + (30 - i),
                'estimated_competition': 'High',
                'estimated_volume': 'High'
            })
    
    # Tier 3: Top 20 services for medium cities
    for city, count in tiers['tier3']:
        for i, service in enumerate(services[:20]):
            priority_combinations.append({
                'city': city,
                'service': service,
                'tier': 3,
                'priority_score': 600 + count + (20 - i),
                'estimated_competition': 'Medium',
                'estimated_volume': 'Medium'
            })
    
    # Tier 4: Top 15 services for small cities
    for city, count in tiers['tier4']:
        for i, service in enumerate(services[:15]):
            priority_combinations.append({
                'city': city,
                'service': service,
                'tier': 4,
                'priority_score': 400 + count + (15 - i),
                'estimated_competition': 'Low-Medium',
                'estimated_volume': 'Medium'
            })
    
    # Tier 5: Top 10 services for towns
    for city, count in tiers['tier5']:
        for i, service in enumerate(services[:10]):
            priority_combinations.append({
                'city': city,
                'service': service,
                'tier': 5,
                'priority_score': 200 + count + (10 - i),
                'estimated_competition': 'Low',
                'estimated_volume': 'Low-Medium'
            })
    
    # Tier 6: Top 5 services for small towns
    for city, count in tiers['tier6']:
        for i, service in enumerate(services[:5]):
            priority_combinations.append({
                'city': city,
                'service': service,
                'tier': 6,
                'priority_score': count + (5 - i),
                'estimated_competition': 'Very Low',
                'estimated_volume': 'Low'
            })
    
    # Sort by priority score
    priority_combinations.sort(key=lambda x: x['priority_score'], reverse=True)
    
    return priority_combinations

def save_opportunities(combinations):
    """Save opportunities to CSV for further analysis"""
    
    # Convert to DataFrame
    df = pd.DataFrame(combinations)
    
    # Add URL structure
    df['url_slug'] = df.apply(lambda row: f"/services/{row['service'].lower().replace(' ', '-')}/{row['city'].lower().replace(' ', '-')}/", axis=1)
    df['page_title'] = df.apply(lambda row: f"{row['service']} in {row['city']}, NC", axis=1)
    
    # Save to CSV
    df.to_csv(OUTPUT_FILE, index=False)
    print(f"\n💾 Saved {len(df)} opportunities to {OUTPUT_FILE}")
    
    return df

def main():
    """Main analysis function"""
    
    print("🚀 YouHealIt SEO Opportunity Analyzer")
    print("="*50)
    
    # Load and analyze data
    result = analyze_location_data()
    if not result:
        return
    
    df, city_counts = result
    
    # Categorize cities
    tiers = categorize_cities(city_counts)
    
    # Calculate strategy
    strategies, total_pages = calculate_seo_strategy(tiers, SERVICES)
    
    # Generate priority combinations
    combinations = generate_priority_combinations(tiers, SERVICES)
    
    # Save results
    results_df = save_opportunities(combinations)
    
    # Show top opportunities
    print(f"\n🏆 TOP 20 HIGHEST PRIORITY OPPORTUNITIES:")
    print("="*80)
    for i, combo in enumerate(combinations[:20], 1):
        print(f"{i:2d}. {combo['service']} in {combo['city']} (Tier {combo['tier']}, Score: {combo['priority_score']})")
    
    # Implementation recommendations
    print(f"\n💡 IMPLEMENTATION RECOMMENDATIONS:")
    print("="*50)
    print("Phase 1 (Start here): Top 100 combinations - Tier 1 & 2 cities, top services")
    print("Phase 2: Next 200 combinations - Expand services in major cities")
    print("Phase 3: Next 500 combinations - Add Tier 3 cities")
    print("Phase 4: Scale to remaining combinations based on performance")
    
    print(f"\n✅ Analysis complete! Check {OUTPUT_FILE} for full results.")

if __name__ == "__main__":
    main()
