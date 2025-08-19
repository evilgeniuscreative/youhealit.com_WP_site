#!/usr/bin/env python3
"""
Database Updater for YouHealIt page_image Column
Updates oz5_posts table with page_image values from CSV
Run this AFTER csv-fetcher.py has processed the CSV
"""

import pandas as pd
import mysql.connector
from mysql.connector import Error
import os
from dotenv import load_dotenv

# Load environment variables
load_dotenv()

# Configuration
CSV_FILE = "assets/big.csv"
TABLE_NAME = "oz5_posts"

# Database configuration - adjust these for your setup
DB_CONFIG = {
    'host': os.getenv('DB_HOST', 'localhost'),
    'database': os.getenv('DB_NAME', 'your_database_name'),
    'user': os.getenv('DB_USER', 'your_username'),
    'password': os.getenv('DB_PASSWORD', 'your_password'),
    'port': os.getenv('DB_PORT', 3306)
}

def test_db_connection():
    """Test database connection"""
    try:
        connection = mysql.connector.connect(**DB_CONFIG)
        if connection.is_connected():
            cursor = connection.cursor()
            cursor.execute("SELECT COUNT(*) FROM oz5_posts")
            count = cursor.fetchone()[0]
            print(f"✅ Database connection successful! Found {count} rows in oz5_posts")
            cursor.close()
            connection.close()
            return True
    except Error as e:
        print(f"❌ Database connection failed: {e}")
        print("\n💡 Make sure to:")
        print("1. Create a .env file with your database credentials:")
        print("   DB_HOST=localhost")
        print("   DB_NAME=your_database_name") 
        print("   DB_USER=your_username")
        print("   DB_PASSWORD=your_password")
        print("   DB_PORT=3306")
        print("2. Or edit the DB_CONFIG dictionary in this script")
        return False

def check_csv_has_page_image():
    """Check if CSV has been processed with page_image column"""
    try:
        df = pd.read_csv(CSV_FILE)
        if 'page_image' not in df.columns:
            print("❌ CSV doesn't have page_image column yet!")
            print("🔄 Run csv-fetcher.py first to generate page_image values")
            return False
        
        # Check if page_image column has values
        empty_count = df['page_image'].isna().sum()
        total_count = len(df)
        filled_count = total_count - empty_count
        
        print(f"📊 CSV Status: {filled_count}/{total_count} rows have page_image values")
        
        if filled_count == 0:
            print("❌ No page_image values found in CSV!")
            print("🔄 Run csv-fetcher.py first to generate page_image values")
            return False
            
        return True, df
        
    except Exception as e:
        print(f"❌ Error reading CSV: {e}")
        return False

def update_database(df):
    """Update oz5_posts table with page_image values"""
    try:
        connection = mysql.connector.connect(**DB_CONFIG)
        cursor = connection.cursor()
        
        # First, check what columns we can use to match rows
        cursor.execute("DESCRIBE oz5_posts")
        columns = [row[0] for row in cursor.fetchall()]
        print(f"📋 Available columns in oz5_posts: {columns[:10]}...")  # Show first 10
        
        # Determine matching strategy
        matching_column = None
        if 'id' in columns and 'id' in df.columns:
            matching_column = 'id'
            print("🎯 Using 'id' column to match rows")
        elif 'youhealit_page' in columns and 'youhealit_page' in df.columns:
            matching_column = 'youhealit_page'
            print("🎯 Using 'youhealit_page' column to match rows")
        else:
            print("❌ Can't find a reliable column to match CSV rows with database rows")
            print("💡 Available options:")
            print("   - Add an 'id' column to your CSV that matches oz5_posts.id")
            print("   - Ensure 'youhealit_page' column exists in both CSV and database")
            return False
        
        # Update rows
        updated_count = 0
        skipped_count = 0
        
        print(f"\n🔄 Starting update process...")
        
        for index, row in df.iterrows():
            try:
                # Skip if no page_image value
                if pd.isna(row['page_image']) or not str(row['page_image']).strip():
                    skipped_count += 1
                    continue
                
                # Skip if no matching column value
                if pd.isna(row[matching_column]) or not str(row[matching_column]).strip():
                    skipped_count += 1
                    continue
                
                # Update the database row
                update_query = f"""
                    UPDATE {TABLE_NAME} 
                    SET page_image = %s 
                    WHERE {matching_column} = %s
                """
                
                cursor.execute(update_query, (str(row['page_image']).strip(), str(row[matching_column])))
                
                if cursor.rowcount > 0:
                    updated_count += 1
                    if updated_count % 100 == 0:
                        print(f"   📝 Updated {updated_count} rows...")
                else:
                    skipped_count += 1
                    
            except Exception as e:
                print(f"⚠️  Error updating row {index}: {e}")
                skipped_count += 1
                continue
        
        # Commit all changes
        connection.commit()
        
        print(f"\n✅ Database update complete!")
        print(f"   📊 {updated_count} rows updated")
        print(f"   ⏭️  {skipped_count} rows skipped")
        
        # Verify the update
        cursor.execute(f"SELECT COUNT(*) FROM {TABLE_NAME} WHERE page_image IS NOT NULL AND page_image != ''")
        db_count = cursor.fetchone()[0]
        print(f"   🔍 Verification: {db_count} rows now have page_image values in database")
        
        cursor.close()
        connection.close()
        return True
        
    except Error as e:
        print(f"❌ Database error: {e}")
        return False

def main():
    """Main function to update database with page_image values"""
    
    print("🚀 YouHealIt Database Updater for page_image Column")
    print("=" * 50)
    
    # Test database connection
    if not test_db_connection():
        return
    
    # Check if CSV has been processed
    csv_result = check_csv_has_page_image()
    if not csv_result:
        return
    
    _, df = csv_result
    
    # Confirm before proceeding
    print(f"\n⚠️  About to update {len(df)} rows in {TABLE_NAME} table")
    print("   This will add page_image values to match the generated image files")
    
    confirm = input("\n❓ Continue? (y/N): ").lower().strip()
    if confirm != 'y':
        print("❌ Update cancelled")
        return
    
    # Update database
    if update_database(df):
        print("\n🎉 Success! Your oz5_posts table now has page_image values")
        print("💡 You can now use them in your web app like:")
        print("   <img src=\"/loc_images/<?php echo $post['page_image']; ?>.webp\">")
    else:
        print("\n❌ Update failed. Check the errors above.")

if __name__ == "__main__":
    main()