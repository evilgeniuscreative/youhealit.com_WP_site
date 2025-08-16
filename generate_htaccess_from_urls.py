# This script reads youhealit_urls.txt and generates .htaccess rewrite rules
def slugify(path):
    if path.endswith('/'):
        return path.strip('/')
    if path.endswith('.php'):
        return path.replace('page-', '').replace('.php', '')
    return path

input_file = "youhealit_urls.txt"
output_file = "htaccess_redirects.txt"

with open(input_file, "r") as f:
    paths = [line.strip() for line in f if line.strip()]

rules = []
for path in paths:
    if path.startswith("/page-") and path.endswith(".php"):
        clean_slug = slugify(path)
        rules.append(f"RewriteRule ^{path[1:]}$ /{clean_slug}/ [R=301,L]")

with open(output_file, "w") as f:
    f.write("# .htaccess rewrite rules generated from youhealit_urls.txt\n")
    f.write("RewriteEngine On\n")
    f.write("RewriteCond %{HTTP_HOST} ^(www\\.)?youhealit\\.com$ [NC]\n")
    for rule in rules:
        f.write(rule + "\n")

print(f"Generated {len(rules)} rewrite rules in {output_file}")
