# images_to_pdf.py
import sys
import os
from PIL import Image

def images_to_pdf(image_folder, output_pdf="output.pdf"):
    image_files = sorted([
        os.path.join(image_folder, f)
        for f in os.listdir(image_folder)
        if f.lower().endswith(('.png', '.jpg', '.jpeg'))
    ])

    if not image_files:
        print("Neboli nájdené žiadne obrázky v zložke.")
        sys.exit(1)

    images = [Image.open(img).convert("RGB") for img in image_files]
    first_image, *rest = images
    first_image.save(output_pdf, save_all=True, append_images=rest)
    print(f"PDF bolo vytvorené: {output_pdf}")

if __name__ == "__main__":
    if len(sys.argv) < 2 or len(sys.argv) > 3:
        print("Použitie: python images_to_pdf.py priecinok_s_obrazkami [vystup.pdf]")
        sys.exit(1)

    input_folder = sys.argv[1]
    output_file = sys.argv[2] if len(sys.argv) == 3 else "output.pdf"
    images_to_pdf(input_folder, output_file)