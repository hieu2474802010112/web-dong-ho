# -*- coding: utf-8 -*-
import fitz
import os

pdf1 = r"C:\Users\admin\Downloads\Lab9_WordPress_Functions_Astra_Updated.pdf"
pdf2 = r"C:\Users\admin\Downloads\Lab 9. Ung dung AI bao mat thong tin Web.pdf"

def extract_pdf_info(path, out_prefix):
    print(f"\n==================== {os.path.basename(path)} ====================")
    doc = fitz.open(path)
    print(f"Total pages: {len(doc)}")
    
    img_dir = rf"C:\xampp\htdocs\luxury-watches\lab-reports\lab9\extracted_images_{out_prefix}"
    os.makedirs(img_dir, exist_ok=True)
    
    for page_num in range(len(doc)):
        page = doc[page_num]
        print(f"\n--- Page {page_num + 1} ---")
        text = page.get_text()
        print(text)
        
        # Extract images
        image_list = page.get_images(full=True)
        print(f"Found {len(image_list)} images on page {page_num + 1}")
        for img_index, img in enumerate(image_list):
            xref = img[0]
            base_image = doc.extract_image(xref)
            image_bytes = base_image["image"]
            image_ext = base_image["ext"]
            img_filename = f"{out_prefix}_p{page_num+1}_img{img_index+1}.{image_ext}"
            img_path = os.path.join(img_dir, img_filename)
            with open(img_path, "wb") as f:
                f.write(image_bytes)
            print(f"  Saved image: {img_filename} ({len(image_bytes)} bytes)")

extract_pdf_info(pdf1, "astra")
extract_pdf_info(pdf2, "ai_sec")
