import sys
import os

try:
    import pypdf
except ImportError:
    print("ERROR: pypdf not installed. Run 'pip install pypdf'")
    sys.exit(1)

def parse_pdf(file_path):
    if not os.path.exists(file_path):
        print(f"ERROR: File not found - {file_path}")
        sys.exit(1)
        
    try:
        reader = pypdf.PdfReader(file_path)
        text = "\n".join([page.extract_text() for page in reader.pages if page.extract_text()])
        print(text)
    except Exception as e:
        print(f"ERROR: {str(e)}")
        sys.exit(1)

if __name__ == '__main__':
    if len(sys.argv) < 2:
        print("ERROR: Missing file path argument.")
        sys.exit(1)
    parse_pdf(sys.argv[1])
