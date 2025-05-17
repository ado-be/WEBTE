# pdf_to_images.py
import sys
import os
import fitz  # PyMuPDF

def pdf_to_images(pdf_path, output_folder="output_images"):
    if not os.path.exists(output_folder):
        os.makedirs(output_folder)

    doc = fitz.open(pdf_path)
    for i, page in enumerate(doc):
        pix = page.get_pixmap()
        output_path = os.path.join(output_folder, f"page_{i+1}.png")
        pix.save(output_path)
        print(f"Uložené: {output_path}")

if __name__ == "__main__":
    if len(sys.argv) < 2 or len(sys.argv) > 3:
        print("Použitie: python pdf_to_images.py subor.pdf [vystupny_priecinok]")
        sys.exit(1)

    folder = sys.argv[2] if len(sys.argv) == 3 else "output_images"
    pdf_to_images(sys.argv[1], folder)
