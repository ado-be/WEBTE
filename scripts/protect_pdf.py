# protect_pdf.py
import sys
from pypdf import PdfReader, PdfWriter

def protect_pdf(input_path, output_path, password):
    reader = PdfReader(input_path)
    writer = PdfWriter()

    for page in reader.pages:
        writer.add_page(page)

    writer.encrypt(password)

    with open(output_path, "wb") as f:
        writer.write(f)

    print(f"PDF súbor bol zabezpečený heslom a uložený ako: {output_path}")

if __name__ == "__main__":
    if len(sys.argv) != 4:
        print("Použitie: python protect_pdf.py vstup.pdf vystup.pdf heslo")
        sys.exit(1)

    input_file = sys.argv[1]
    output_file = sys.argv[2]
    password = sys.argv[3]

    protect_pdf(input_file, output_file, password)
