# extract_page.py
import sys
from pypdf import PdfReader, PdfWriter

def extract_page(pdf_path, page_number, output_path="extracted_page.pdf"):
    reader = PdfReader(pdf_path)
    writer = PdfWriter()

    if page_number < 1 or page_number > len(reader.pages):
        print(f"Chyba: Strana {page_number} neexistuje v dokumente s {len(reader.pages)} stranami.")
        sys.exit(1)

    writer.add_page(reader.pages[page_number - 1])  # indexujeme od 0

    with open(output_path, "wb") as f:
        writer.write(f)

    print(f"Strana {page_number} bola uložená ako: {output_path}")

if __name__ == "__main__":
    if len(sys.argv) < 3 or len(sys.argv) > 4:
        print("Použitie: python extract_page.py subor.pdf cislo_strany [vystup.pdf]")
        sys.exit(1)

    input_file = sys.argv[1]
    page_number = int(sys.argv[2])
    output_file = sys.argv[3] if len(sys.argv) == 4 else "extracted_page.pdf"

    extract_page(input_file, page_number, output_file)