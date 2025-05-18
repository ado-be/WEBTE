# pdf_to_word.py
import sys
from pdf2docx import Converter

def convert_pdf_to_docx(pdf_path, docx_path="output.docx"):
    cv = Converter(pdf_path)
    cv.convert(docx_path, start=0, end=None)
    cv.close()
    print(f"Uložené: {docx_path}")

if __name__ == "__main__":
    if len(sys.argv) < 2 or len(sys.argv) > 3:
        print("Použitie: python pdf_to_word.py vstup.pdf [vystup.docx]")
        sys.exit(1)

    input_file = sys.argv[1]
    output_file = sys.argv[2] if len(sys.argv) == 3 else "output.docx"
    convert_pdf_to_docx(input_file, output_file)
