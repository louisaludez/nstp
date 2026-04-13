import os
import re

def update_files():
    base_dir = r"c:\Users\USER\Desktop\nstp"
    dirs = ['admin', 'instructor']
    
    # Regex designed to cautiously match the topbar block.
    # It starts at the topbar container and matches up to the first closing </div> that precedes
    # either a <div class="mb-4">, <h3, or <?php if ($message...
    pattern = re.compile(
        r'<div class="d-flex justify-content-between align-items-center mb-4[^>]*>\s*<h5[^>]*>(?:Coordinator Portal|Instructor Portal)</h5>.*?</div>\s*</div>\s*(?:</div>\s*)?(?=\n\s*(?:<div class="mb-4|<h3|<(?:div|\?php) [^>]*message))',
        re.DOTALL
    )

    for d in dirs:
        d_path = os.path.join(base_dir, d)
        for f in os.listdir(d_path):
            if f.endswith('.php'):
                f_path = os.path.join(d_path, f)
                with open(f_path, 'r', encoding='utf-8') as file:
                    content = file.read()
                
                new_content = pattern.sub(r'<?php include \'../includes/topbar.php\'; ?>', content)
                
                if new_content != content:
                    print(f"Updated {f_path}")
                    with open(f_path, 'w', encoding='utf-8') as file:
                        file.write(new_content)
                else:
                    print(f"Skipped {f_path}")

if __name__ == "__main__":
    update_files()
