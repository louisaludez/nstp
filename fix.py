import os

base_dir = r"c:\Users\USER\Desktop\nstp"
for root, _, files in os.walk(base_dir):
    for f in files:
        if f.endswith('.php'):
            p = os.path.join(root, f)
            with open(p, 'r', encoding='utf-8') as file:
                content = file.read()
            
            to_find = r"<?php include \'" + "../includes/topbar.php" + r"\'; ?>"
            to_repl = "<?php include '../includes/topbar.php'; ?>"
            
            if to_find in content:
                new_content = content.replace(to_find, to_repl)
                with open(p, 'w', encoding='utf-8') as file:
                    file.write(new_content)
                print(f"Fixed {p}")
