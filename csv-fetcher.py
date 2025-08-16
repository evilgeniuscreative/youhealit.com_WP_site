#!/usr/bin/env python3
"""
Image Fetcher for YouHealIt CSV Data
Fetches images from Wikipedia/Wikimedia or finds neighborhood images
Saves as WebP format in /loc_images/ directory
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

# Create output directory
os.makedirs(OUTPUT_DIR, exist_ok=True)

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
    """Search for neighborhood/city images using Google Images-like approach"""
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
    except Exception as e:
        print(f"Error loading CSV: {e}")
        return
    
    # Process each row
    for index, row in df.iterrows():
        try:
            # Create filename from youhealit_page or city info
            if 'youhealit_page' in row and pd.notna(row['youhealit_page']):
                filename = row['youhealit_page'].strip('/')
                filename = re.sub(r'[^a-zA-Z0-9\-_/]', '', filename)
                filename = filename.replace('/', '-') + '.webp'
            else:
                city_name = row.get('city_name', f'city_{index}')
                filename = re.sub(r'[^a-zA-Z0-9\-_]', '-', city_name.lower()) + '.webp'
            
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
            
            # If no Wikimedia image, search for neighborhood/city image
            if not image_found:
                city_name = row.get('city_name', '')
                neighborhood = row.get('city_section_name', '')
                
                img_url = search_neighborhood_image(city_name, neighborhood)
                if img_url:
                    if download_and_convert_image(img_url, output_path):
                        print(f"✅ Downloaded neighborhood image: {filename}")
                        image_found = True
            
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
    
    print("🎉 Image fetching complete!")

if __name__ == "__main__":
    main()