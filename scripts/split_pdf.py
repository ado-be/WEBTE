# split_pdf.py
import sys
from pypdf import PdfReader, PdfWriter

def split_pdf(pdf_path, split_at, output1="split_part1.pdf", output2="split_part2.pdf"):
    reader = PdfReader(pdf_path)
    total_pages = len(reader.pages)

    if not (0 < split_at < total_pages):
        print(f"Chyba: rozdelenie musí byť v rozsahu 1 až {total_pages - 1}")
        sys.exit(1)

    writer1 = PdfWriter()
    writer2 = PdfWriter()

    for i in range(split_at):
        writer1.add_page(reader.pages[i])

    for i in range(split_at, total_pages):
        writer2.add_page(reader.pages[i])

    with open(output1, "wb") as f1:
        writer1.write(f1)
        print(f"Uložené: {output1}")

    with open(output2, "wb") as f2:
        writer2.write(f2)
        print(f"Uložené: {output2}")

if __name__ == "__main__":
    if len(sys.argv) < 3 or len(sys.argv) > 5:
        print("Použitie: python split_pdf.py subor.pdf cislo_strany [vystup1.pdf vystup2.pdf]")
        sys.exit(1)

    input_file = sys.argv[1]
    split_at = int(sys.argv[2])
    output1 = sys.argv[3] if len(sys.argv) >= 4 else "split_part1.pdf"
    output2 = sys.argv[4] if len(sys.argv) == 5 else "split_part2.pdf"

    split_pdf(input_file, split_at, output1, output2)