import os

base_dir = r"c:\Users\USER\Desktop\nstp"
dirs = ['admin', 'instructor']

for d in dirs:
    d_path = os.path.join(base_dir, d)
    for f in os.listdir(d_path):
        if not f.endswith('.php'): continue
        f_path = os.path.join(d_path, f)
        with open(f_path, 'r', encoding='utf-8') as file:
            content = file.read()
        
        if "<?php include '../includes/topbar.php'; ?>" in content: continue

        # The header div usually starts with justify-content-between
        i = content.find('justify-content-between align-items-center')
        if i == -1: continue
        
        div_start = content.rfind('<div', 0, i)
        
        depth = 0
        end_idx = -1
        j = div_start
        while j < len(content):
            if content[j:j+4] == '<div':
                depth += 1
                j += 4
            elif content[j:j+6] == '</div>':
                depth -= 1
                if depth == 0:
                    end_idx = j + 6
                    break
                j += 6
            else:
                j += 1
        
        if end_idx != -1:
            extract = content[div_start:end_idx]
            if 'Portal' in extract and 'bi-bell' in extract:
                new_content = content[:div_start] + "<?php include '../includes/topbar.php'; ?>" + content[end_idx:]
                with open(f_path, 'w', encoding='utf-8') as file:
                    file.write(new_content)
                print(f"Updated {f_path}")
            else:
                print(f"Skipped {f_path} - no portal match inside top div")
