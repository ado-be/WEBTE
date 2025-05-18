# merge_pdf.py
import sys
from pypdf import PdfReader, PdfWriter

def merge_pdfs(pdf1_path, pdf2_path, output_path="merged_output.pdf"):
    writer = PdfWriter()

    for path in [pdf1_path, pdf2_path]:
        reader = PdfReader(path)
        for page in reader.pages:
            writer.add_page(page)

    with open(output_path, "wb") as f:
        writer.write(f)

    print(output_path)

if __name__ == "__main__":
    if len(sys.argv) < 3 or len(sys.argv) > 4:
        print("Použitie: python merge_pdf.py subor1.pdf subor2.pdf [vystup.pdf]")
        sys.exit(1)

    output = sys.argv[3] if len(sys.argv) == 4 else "merged_output.pdf"
    merge_pdfs(sys.argv[1], sys.argv[2], output)
