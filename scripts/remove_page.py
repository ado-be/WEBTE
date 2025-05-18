# remove_page.py
import sys
from pypdf import PdfReader, PdfWriter

def remove_page(pdf_path, page_index, output_path="output_removed.pdf"):
    reader = PdfReader(pdf_path)
    writer = PdfWriter()

    for i, page in enumerate(reader.pages):
        if i != page_index:
            writer.add_page(page)

    with open(output_path, "wb") as f:
        writer.write(f)

    print(output_path)

if __name__ == "__main__":
    if len(sys.argv) < 3 or len(sys.argv) > 4:
        print("Použitie: python remove_page.py subor.pdf cislo_strany [vystup.pdf]")
        sys.exit(1)

    output = sys.argv[3] if len(sys.argv) == 4 else "output_removed.pdf"
    remove_page(sys.argv[1], int(sys.argv[2]), output)
