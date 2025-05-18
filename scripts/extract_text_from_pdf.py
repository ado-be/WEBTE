# extract_text_from_pdf.py
import sys
import fitz  # PyMuPDF

def extract_text(pdf_path, output_path="extracted_text.txt"):
    doc = fitz.open(pdf_path)
    with open(output_path, "w", encoding="utf-8") as f:
        for i, page in enumerate(doc):
            text = page.get_text()
            f.write(f"\n--- Strana {i+1} ---\n")
            f.write(text)
    print(f"Text bol extrahovaný do súboru: {output_path}")

if __name__ == "__main__":
    if len(sys.argv) < 2 or len(sys.argv) > 3:
        print("Použitie: python extract_text_from_pdf.py subor.pdf [vystup.txt]")
        sys.exit(1)

    input_file = sys.argv[1]
    output_file = sys.argv[2] if len(sys.argv) == 3 else "extracted_text.txt"
    extract_text(input_file, output_file)
