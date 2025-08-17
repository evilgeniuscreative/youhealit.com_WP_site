#!/usr/bin/env python3
"""
Image Fetcher for YouHealIt CSV Data
Fetches images from Wikipedia/Wikimedia or finds neighborhood images
Saves as WebP format in /loc_images/ directory
Uses database ID or dedicated image_filename column for clean implementation
"""

import requests
from bs4 import BeautifulSoup
import os
import pandas as pd
from PIL import Image
from io import BytesIO
import time
import urllib.parse
import re

# Configuration
CSV_FILE = "assets/big.csv"
OUTPUT_DIR = "loc_images"
PLACEHOLDER_PATH = "placeholder.webp"
MAX_RETRIES = 3
DELAY_BETWEEN_REQUESTS = 1  # seconds

# Using simple city+neighborhood naming strategy
# Generates names like: durhamdowntown.webp, raleighbrier-creek.webp
NAMING_STRATEGY = 'simple_slug'

# Create output directory
os.makedirs(OUTPUT_DIR, exist_ok=True)

def generate_image_filename(row, index):
    """Generate clean image filename: cityneighborhood.webp or city001.webp"""
    
    # Use dedicated page_image column if it exists and has a value
    if 'page_image' in row and pd.notna(row['page_image']) and str(row['page_image']).strip():
        filename = str(row['page_image']).strip()
        if not filename.endswith('.webp'):
            filename += '.webp'
        return filename
    
    # Generate from city + neighborhood data (this shouldn't happen during normal operation
    # since we populate page_image column first, but keeping as fallback)
    city = ''
    if 'city_name' in row and pd.notna(row['city_name']):
        city = str(row['city_name']).lower()
        city = re.sub(r'[^a-z0-9]', '', city)
    
    neighborhood = ''
    if 'city_section_name' in row and pd.notna(row['city_section_name']):
        neighborhood = str(row['city_section_name']).lower()
        neighborhood = re.sub(r'[^a-z0-9]', '', neighborhood)
    
    if city and neighborhood:
        return city + neighborhood + '.webp'
    elif city:
        return f"{city}{index:03d}.webp"  # Simple fallback numbering
    
    return f"location{index + 1}.webp"

def generate_simple_slug_with_numbering(row, index, city_counts):
    """Generate simple slug with city numbering: durham.webp, durham001.webp, etc."""
    
    # Get city name
    city = ''
    if 'city_name' in row and pd.notna(row['city_name']):
        city = str(row['city_name']).lower()
        city = re.sub(r'[^a-z0-9]', '', city)  # Remove all non-alphanumeric
    
    # Get neighborhood/section
    neighborhood = ''
    if 'city_section_name' in row and pd.notna(row['city_section_name']):
        neighborhood = str(row['city_section_name']).lower()
        neighborhood = re.sub(r'[^a-z0-9]', '', neighborhood)  # Remove all non-alphanumeric
    
    # Generate filename
    if city and neighborhood:
        # Has both city and neighborhood: durhamdowntown.webp
        filename = city + neighborhood + '.webp'
        return filename
    
    elif city:
        # Only city, need to number: durham.webp, durham001.webp, etc.
        if city not in city_counts:
            city_counts[city] = 0
            # First occurrence gets no number
            filename = city + '.webp'
        else:
            # Subsequent occurrences get numbered
            city_counts[city] += 1
            filename = f"{city}{city_counts[city]:03d}.webp"
        
        return filename
    
    # Fallback if no city data
    return f"location{index + 1}.webp"

def update_csv_with_page_image_column(df):
    """Add page_image column to CSV with simple filenames and city numbering"""
    if 'page_image' not in df.columns:
        df['page_image'] = ''
        
        # Track city counts for numbering
        city_counts = {}
        
        for index, row in df.iterrows():
            # Generate the simple filename (without .webp extension for database storage)
            filename = generate_simple_slug_with_numbering(row, index, city_counts)
            filename_without_ext = filename.replace('.webp', '')
            df.at[index, 'page_image'] = filename_without_ext
        
        # Save updated CSV
        backup_file = CSV_FILE.replace('.csv', '_backup.csv')
        df.to_csv(backup_file, index=False)
        print(f"💾 Created backup: {backup_file}")
        
        df.to_csv(CSV_FILE, index=False)
        print(f"📝 Updated CSV with page_image column")
        print(f"📝 Sample filenames: {df['page_image'].head(10).tolist()}")
    
    return df

def get_wikimedia_image(wikimedia_url):
    """Extract main image from Wikimedia page"""
    if not wikimedia_url or pd.isna(wikimedia_url):
        return None
    
    try:
        response = requests.get(wikimedia_url, timeout=10)
        soup = BeautifulSoup(response.content, 'html.parser')
        
        # Look for main image in different locations
        image_selectors = [
            'img.mw-file-element',  # Main file page image
            '.fullImageLink img',   # Full size image link
            '.thumbinner img',      # Thumbnail image
            'img[src*="upload.wikimedia"]'  # Any Wikimedia uploaded image
        ]
        
        for selector in image_selectors:
            img_tag = soup.select_one(selector)
            if img_tag and img_tag.get('src'):
                img_url = img_tag['src']
                if img_url.startswith('//'):
                    img_url = 'https:' + img_url
                elif img_url.startswith('/'):
                    img_url = 'https://commons.wikimedia.org' + img_url
                return img_url
                
    except Exception as e:
        print(f"Error fetching Wikimedia image: {e}")
    
    return None

def search_neighborhood_image(city_name, neighborhood=None):
    """Search for neighborhood/city images using Wikipedia"""
    try:
        # Construct search query
        if neighborhood:
            query = f"{neighborhood} {city_name} North Carolina neighborhood"
        else:
            query = f"{city_name} North Carolina city"
        
        # Use Wikipedia as a reliable source
        wiki_search_url = f"https://en.wikipedia.org/wiki/{urllib.parse.quote(city_name.replace(' ', '_'))}"
        
        response = requests.get(wiki_search_url, timeout=10)
        if response.status_code == 200:
            soup = BeautifulSoup(response.content, 'html.parser')
            
            # Look for main infobox image
            infobox_img = soup.select_one('.infobox img, .vcard img')
            if infobox_img and infobox_img.get('src'):
                img_url = infobox_img['src']
                if img_url.startswith('//'):
                    img_url = 'https:' + img_url
                return img_url
                
            # Look for any image in the article
            content_img = soup.select_one('.mw-parser-output img[src*="upload.wikimedia"]')
            if content_img and content_img.get('src'):
                img_url = content_img['src']
                if img_url.startswith('//'):
                    img_url = 'https:' + img_url
                return img_url
                
    except Exception as e:
        print(f"Error searching for neighborhood image: {e}")
    
    return None

def download_and_convert_image(img_url, output_path):
    """Download image and convert to WebP format"""
    try:
        response = requests.get(img_url, stream=True, timeout=15)
        if response.status_code == 200:
            # Open image and convert to WebP
            img = Image.open(BytesIO(response.content))
            
            # Convert to RGB if necessary
            if img.mode in ('RGBA', 'LA', 'P'):
                img = img.convert('RGB')
            
            # Resize if too large (max 1200px width)
            if img.width > 1200:
                ratio = 1200 / img.width
                new_height = int(img.height * ratio)
                img = img.resize((1200, new_height), Image.Resampling.LANCZOS)
            
            # Save as WebP
            img.save(output_path, 'WebP', quality=85, optimize=True)
            return True
            
    except Exception as e:
        print(f"Error downloading/converting image: {e}")
    
    return False

def create_placeholder_if_needed():
    """Create a simple placeholder image if none exists"""
    if not os.path.exists(PLACEHOLDER_PATH):
        # Create a simple colored placeholder
        img = Image.new('RGB', (800, 600), color=(139, 195, 74))  # Green color
        img.save(PLACEHOLDER_PATH, 'WebP', quality=85)

def main():
    """Main function to process CSV and fetch images"""
    
    # Create placeholder
    create_placeholder_if_needed()
    
    # Load CSV data
    try:
        df = pd.read_csv(CSV_FILE)
        print(f"Loaded {len(df)} rows from CSV")
        print(f"Columns: {list(df.columns)}")
        print(f"Using simple slug naming strategy")
    except Exception as e:
        print(f"Error loading CSV: {e}")
        return
    
    # Update CSV with page_image column
    df = update_csv_with_page_image_column(df)
    
    # Process each row
    successful_downloads = 0
    for index, row in df.iterrows():
        try:
            # Generate clean filename
            filename = generate_image_filename(row, index)
            output_path = os.path.join(OUTPUT_DIR, filename)
            
            # Skip if file already exists
            if os.path.exists(output_path):
                print(f"✅ Skipping {filename} (already exists)")
                continue
            
            print(f"🔍 Processing {filename} ({index + 1}/{len(df)})")
            
            image_found = False
            
            # Try Wikimedia first
            if 'wikimedia_page' in row and pd.notna(row['wikimedia_page']):
                img_url = get_wikimedia_image(row['wikimedia_page'])
                if img_url:
                    if download_and_convert_image(img_url, output_path):
                        print(f"✅ Downloaded from Wikimedia: {filename}")
                        image_found = True
                        successful_downloads += 1
            
            # If no Wikimedia image, search for neighborhood/city image
            if not image_found:
                city_name = row.get('city_name', '')
                neighborhood = row.get('city_section_name', '')
                
                img_url = search_neighborhood_image(city_name, neighborhood)
                if img_url:
                    if download_and_convert_image(img_url, output_path):
                        print(f"✅ Downloaded neighborhood image: {filename}")
                        image_found = True
                        successful_downloads += 1
            
            # Use placeholder if no image found
            if not image_found:
                if os.path.exists(PLACEHOLDER_PATH):
                    placeholder_img = Image.open(PLACEHOLDER_PATH)
                    placeholder_img.save(output_path, 'WebP', quality=85)
                    print(f"⚠️ Used placeholder: {filename}")
                else:
                    print(f"❌ No image found for: {filename}")
            
            # Rate limiting
            time.sleep(DELAY_BETWEEN_REQUESTS)
            
        except Exception as e:
            print(f"❌ Error processing row {index}: {e}")
            continue
    
    print(f"🎉 Image fetching complete!")
    print(f"📊 Successfully downloaded {successful_downloads} images")
    print(f"📁 Images saved to: {OUTPUT_DIR}/")

if __name__ == "__main__":
    main()