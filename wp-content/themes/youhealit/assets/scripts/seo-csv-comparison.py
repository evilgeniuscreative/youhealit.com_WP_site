#!/usr/bin/env python3
"""
CSV vs SEO Opportunities Comparison Tool
Compares your existing CSV data against recommended SEO opportunities
Shows what to add, remove, or keep for optimal SEO strategy
"""

import pandas as pd
import numpy as np
from collections import defaultdict
import re

# Configuration
EXISTING_CSV = "big.csv"
OPPORTUNITIES_CSV = "seo_opportunities.csv"
OUTPUT_COMPARISON = "seo_comparison_results.csv"

def load_existing_data():
    """Load and analyze existing CSV data"""
    
    print("📂 Loading existing data...")
    
    try:
        df = pd.read_csv(EXISTING_CSV)
        print(f"✅ Loaded {len(df):,} existing locations")
        
        # Extract service from headlines or content
        services_found = set()
        for _, row in df.iterrows():
            headline = str(row.get('city_headline', ''))
            
            # Extract service from headlines like "Downtown Durham Chiropractic Care"
            # This is a basic extraction - you might need to refine based on your data
            for service_word in ['Chiropractic', 'Physical Therapy', 'Massage', 'Mental Health', 
                               'Acupuncture', 'Pain Management', 'Weight Loss', 'Wellness']:
                if service_word.lower() in headline.lower():
                    services_found.add(service_word)
        
        print(f"🔍 Services detected in existing data: {len(services_found)}")
        for service in sorted(services_found):
            print(f"   • {service}")
        
        return df, services_found
        
    except Exception as e:
        print(f"❌ Error loading existing CSV: {e}")
        return None, None

def load_recommended_opportunities():
    """Load recommended SEO opportunities"""
    
    print(f"\n📋 Loading SEO recommendations...")
    
    try:
        df = pd.read_csv(OPPORTUNITIES_CSV)
        print(f"✅ Loaded {len(df):,} recommended opportunities")
        
        # Group by tier
        tier_counts = df['tier'].value_counts().sort_index()
        print(f"\n🎯 Recommendations by tier:")
        for tier, count in tier_counts.items():
            print(f"   Tier {tier}: {count:,} opportunities")
        
        return df
        
    except Exception as e:
        print(f"❌ Error loading opportunities CSV: {e}")
        print("💡 Make sure to run the SEO analyzer first!")
        return None

def compare_coverage(existing_df, opportunities_df):
    """Compare existing coverage vs recommended opportunities"""
    
    print(f"\n🔍 COVERAGE ANALYSIS:")
    print("="*50)
    
    # Get existing city coverage
    existing_cities = set(existing_df['city_name'].unique())
    recommended_cities = set(opportunities_df['city'].unique())
    
    print(f"Cities in existing data: {len(existing_cities)}")
    print(f"Cities in recommendations: {len(recommended_cities)}")
    
    # Find overlaps and gaps
    cities_covered = existing_cities.intersection(recommended_cities)
    cities_missing = recommended_cities - existing_cities
    cities_extra = existing_cities - recommended_cities
    
    print(f"\n✅ Cities you already cover: {len(cities_covered)}")
    print(f"❌ High-opportunity cities you're missing: {len(cities_missing)}")
    print(f"⚠️  Cities you have but not in recommendations: {len(cities_extra)}")
    
    if cities_missing:
        print(f"\n🎯 TOP MISSING HIGH-OPPORTUNITY CITIES:")
        # Get tier info for missing cities
        missing_with_tiers = opportunities_df[opportunities_df['city'].isin(cities_missing)][['city', 'tier']].drop_duplicates()
        missing_sorted = missing_with_tiers.sort_values('tier')
        
        for _, row in missing_sorted.head(15).iterrows():
            tier_name = {1: 'Major Metro', 2: 'Large City', 3: 'Medium City', 4: 'Small City', 5: 'Town', 6: 'Small Town'}[row['tier']]
            print(f"   • {row['city']} (Tier {row['tier']} - {tier_name})")
    
    return {
        'cities_covered': cities_covered,
        'cities_missing': cities_missing,
        'cities_extra': cities_extra
    }

def analyze_service_gaps(existing_df, opportunities_df):
    """Analyze service coverage gaps"""
    
    print(f"\n🔧 SERVICE ANALYSIS:")
    print("="*50)
    
    # Current approach: assuming all locations are chiropractic-focused
    # In reality, you'd extract actual services from your data
    
    recommended_services = set(opportunities_df['service'].unique())
    print(f"Recommended services: {len(recommended_services)}")
    
    # Top recommended services by frequency
    service_freq = opportunities_df['service'].value_counts()
    print(f"\n🏆 TOP 15 RECOMMENDED SERVICES BY OPPORTUNITY COUNT:")
    for i, (service, count) in enumerate(service_freq.head(15).items(), 1):
        print(f"   {i:2d}. {service:<25} {count:4d} opportunities")
    
    return service_freq

def generate_action_plan(comparison_data, opportunities_df):
    """Generate specific action plan"""
    
    print(f"\n📋 ACTION PLAN:")
    print("="*60)
    
    # Priority actions
    print(f"🚀 PHASE 1 - IMMEDIATE WINS (Next 30 days):")
    print(f"   1. Add missing Tier 1 cities (if any)")
    print(f"   2. Expand to top 5 services in your existing major cities")
    print(f"   3. Create 50-100 high-priority pages")
    
    # Get top opportunities for existing cities
    existing_cities = comparison_data['cities_covered']
    existing_opps = opportunities_df[opportunities_df['city'].isin(existing_cities)]
    top_existing_opps = existing_opps.nlargest(20, 'priority_score')
    
    print(f"\n   🎯 Start with these combinations in cities you already cover:")
    for _, opp in top_existing_opps.head(10).iterrows():
        print(f"      • {opp['service']} in {opp['city']} (Score: {opp['priority_score']})")
    
    print(f"\n🎯 PHASE 2 - EXPANSION (Next 90 days):")
    print(f"   1. Add missing high-tier cities")
    print(f"   2. Expand service coverage in existing cities")
    print(f"   3. Target 200-500 total strategic pages")
    
    # Missing cities by tier
    missing_cities = comparison_data['cities_missing']
    if missing_cities:
        missing_opps = opportunities_df[opportunities_df['city'].isin(missing_cities)]
        top_missing = missing_opps.nlargest(10, 'priority_score')
        
        print(f"\n   🏙️ Priority cities to add:")
        for _, opp in top_missing.iterrows():
            tier_name = {1: 'Major Metro', 2: 'Large City', 3: 'Medium City', 4: 'Small City', 5: 'Town', 6: 'Small Town'}[opp['tier']]
            print(f"      • {opp['city']} (Tier {opp['tier']} - {tier_name})")
    
    print(f"\n📊 PHASE 3 - OPTIMIZATION (Ongoing):")
    print(f"   1. Monitor performance of Phase 1-2 pages")
    print(f"   2. Double down on what works")
    print(f"   3. Pause/remove underperforming combinations")
    print(f"   4. Scale to full strategic recommendations based on results")

def create_implementation_csv(opportunities_df, comparison_data):
    """Create prioritized implementation CSV"""
    
    # Mark opportunities based on current coverage
    existing_cities = comparison_data['cities_covered']
    
    opportunities_df['implementation_phase'] = 'Phase 3'
    opportunities_df['current_coverage'] = opportunities_df['city'].isin(existing_cities)
    
    # Phase 1: Top opportunities in existing cities
    phase1_mask = (
        (opportunities_df['current_coverage'] == True) & 
        (opportunities_df['tier'] <= 3) &
        (opportunities_df['priority_score'] >= opportunities_df['priority_score'].quantile(0.8))
    )
    opportunities_df.loc[phase1_mask, 'implementation_phase'] = 'Phase 1'
    
    # Phase 2: Missing high-tier cities + more services in existing cities
    phase2_mask = (
        ((opportunities_df['current_coverage'] == False) & (opportunities_df['tier'] <= 2)) |
        ((opportunities_df['current_coverage'] == True) & (opportunities_df['tier'] <= 4) & 
         (opportunities_df['priority_score'] >= opportunities_df['priority_score'].quantile(0.6)))
    )
    opportunities_df.loc[phase2_mask & ~phase1_mask, 'implementation_phase'] = 'Phase 2'
    
    # Add implementation priority within each phase
    opportunities_df['phase_priority'] = opportunities_df.groupby('implementation_phase')['priority_score'].rank(method='dense', ascending=False).astype(int)
    
    # Sort by phase and priority
    phase_order = {'Phase 1': 1, 'Phase 2': 2, 'Phase 3': 3}
    opportunities_df['phase_order'] = opportunities_df['implementation_phase'].map(phase_order)
    opportunities_df = opportunities_df.sort_values(['phase_order', 'phase_priority'])
    
    # Save implementation plan
    implementation_cols = [
        'implementation_phase', 'phase_priority', 'city', 'service', 'tier', 
        'priority_score', 'current_coverage', 'estimated_competition', 
        'estimated_volume', 'url_slug', 'page_title'
    ]
    
    opportunities_df[implementation_cols].to_csv(OUTPUT_COMPARISON, index=False)
    
    # Show phase breakdown
    phase_counts = opportunities_df['implementation_phase'].value_counts()
    print(f"\n📊 IMPLEMENTATION PHASES:")
    for phase in ['Phase 1', 'Phase 2', 'Phase 3']:
        count = phase_counts.get(phase, 0)
        print(f"   {phase}: {count:,} opportunities")
    
    print(f"\n💾 Saved detailed implementation plan to {OUTPUT_COMPARISON}")

def main():
    """Main comparison function"""
    
    print("🔍 YouHealIt CSV vs SEO Opportunities Comparison")
    print("="*60)
    
    # Load data
    existing_df, existing_services = load_existing_data()
    if existing_df is None:
        return
    
    opportunities_df = load_recommended_opportunities()
    if opportunities_df is None:
        return
    
    # Compare coverage
    comparison_data = compare_coverage(existing_df, opportunities_df)
    
    # Analyze services
    service_analysis = analyze_service_gaps(existing_df, opportunities_df)
    
    # Generate action plan
    generate_action_plan(comparison_data, opportunities_df)
    
    # Create implementation CSV
    create_implementation_csv(opportunities_df, comparison_data)
    
    print(f"\n✅ Comparison complete!")
    print(f"📋 Next steps:")
    print(f"   1. Review {OUTPUT_COMPARISON} for detailed implementation plan")
    print(f"   2. Start with Phase 1 opportunities (immediate wins)")
    print(f"   3. Monitor performance before scaling to Phase 2-3")

if __name__ == "__main__":
    main()