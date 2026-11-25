import os
import re
from docx import Document
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT, WD_ALIGN_VERTICAL
from docx.oxml import OxmlElement, parse_xml
from docx.oxml.ns import nsdecls, qn

def set_cell_background(cell, fill_hex):
    tcPr = cell._tc.get_or_add_tcPr()
    shd = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{fill_hex}"/>')
    tcPr.append(shd)

def set_cell_margins(cell, top=100, bottom=100, left=150, right=150):
    tcPr = cell._tc.get_or_add_tcPr()
    tcMar = parse_xml(f'<w:tcMar {nsdecls("w")}><w:top w:w="{top}" w:type="dxa"/><w:bottom w:w="{bottom}" w:type="dxa"/><w:left w:w="{left}" w:type="dxa"/><w:right w:w="{right}" w:type="dxa"/></w:tcMar>')
    tcPr.append(tcMar)

def create_tutorial_docx(md_path, docx_path):
    with open(md_path, 'r', encoding='utf-8') as f:
        md_text = f.read()

    doc = Document()

    # Set margins: 1 inch (72 pt)
    sections = doc.sections
    for section in sections:
        section.top_margin = Inches(0.8)
        section.bottom_margin = Inches(0.8)
        section.left_margin = Inches(0.8)
        section.right_margin = Inches(0.8)

    # Base Normal Style
    style_normal = doc.styles['Normal']
    font = style_normal.font
    font.name = 'Calibri'
    font.size = Pt(11)
    font.color.rgb = RGBColor(0x1F, 0x29, 0x37)

    lines = md_text.split('\n')
    i = 0
    in_code_block = False
    code_lines = []
    in_table = False
    table_lines = []

    def flush_code_block():
        nonlocal code_lines
        if code_lines:
            tbl = doc.add_table(rows=1, cols=1)
            tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
            cell = tbl.cell(0, 0)
            set_cell_background(cell, "F3F4F6")
            set_cell_margins(cell, top=120, bottom=120, left=180, right=180)
            p = cell.paragraphs[0]
            p.paragraph_format.space_before = Pt(2)
            p.paragraph_format.space_after = Pt(2)
            p.paragraph_format.line_spacing = 1.15
            run = p.add_run('\n'.join(code_lines))
            run.font.name = 'Consolas'
            run.font.size = Pt(9.5)
            run.font.color.rgb = RGBColor(0x11, 0x18, 0x27)
            doc.add_paragraph().paragraph_format.space_after = Pt(4)
            code_lines = []

    def flush_table():
        nonlocal table_lines
        if not table_lines:
            return
        
        # parse rows
        rows_data = []
        for tl in table_lines:
            if re.match(r'^\s*\|?\s*[-:]+[-| :]*$', tl):
                continue
            cells = [c.strip() for c in tl.strip().strip('|').split('|')]
            rows_data.append(cells)

        if rows_data:
            num_cols = max(len(r) for r in rows_data)
            tbl = doc.add_table(rows=len(rows_data), cols=num_cols)
            tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
            tbl.autofit = True

            for r_idx, r_data in enumerate(rows_data):
                is_header = (r_idx == 0)
                for c_idx in range(num_cols):
                    cell = tbl.cell(r_idx, c_idx)
                    val = r_data[c_idx] if c_idx < len(r_data) else ""
                    set_cell_margins(cell, top=80, bottom=80, left=120, right=120)
                    if is_header:
                        set_cell_background(cell, "1E3A8A") # Navy Blue
                        p = cell.paragraphs[0]
                        p.paragraph_format.space_before = Pt(3)
                        p.paragraph_format.space_after = Pt(3)
                        run = p.add_run(val)
                        run.font.bold = True
                        run.font.color.rgb = RGBColor(0xFF, 0xFF, 0xFF)
                        run.font.size = Pt(9.5)
                    else:
                        bg_col = "F9FAFB" if r_idx % 2 == 1 else "FFFFFF"
                        set_cell_background(cell, bg_col)
                        p = cell.paragraphs[0]
                        p.paragraph_format.space_before = Pt(2)
                        p.paragraph_format.space_after = Pt(2)
                        
                        # formatting bold inline
                        parts = re.split(r'(\*\*.*?\*\*)', val)
                        for part in parts:
                            if part.startswith('**') and part.endswith('**'):
                                r = p.add_run(part[2:-2])
                                r.font.bold = True
                            else:
                                r = p.add_run(part)
                            r.font.size = Pt(9)
                            r.font.color.rgb = RGBColor(0x37, 0x41, 0x51)
            doc.add_paragraph().paragraph_format.space_after = Pt(4)
        table_lines = []

    while i < len(lines):
        line = lines[i]

        # Code block fence
        if line.strip().startswith('```'):
            if in_code_block:
                in_code_block = False
                flush_code_block()
            else:
                if in_table:
                    in_table = False
                    flush_table()
                in_code_block = True
                code_lines = []
            i += 1
            continue

        if in_code_block:
            code_lines.append(line)
            i += 1
            continue

        # Table detection
        if line.strip().startswith('|'):
            in_table = True
            table_lines.append(line)
            i += 1
            continue
        else:
            if in_table:
                in_table = False
                flush_table()

        # Headings
        if line.startswith('# '):
            h1 = doc.add_heading(line[2:].strip(), level=1)
            h1.paragraph_format.space_before = Pt(14)
            h1.paragraph_format.space_after = Pt(6)
            for run in h1.runs:
                run.font.name = 'Calibri'
                run.font.size = Pt(18)
                run.font.bold = True
                run.font.color.rgb = RGBColor(0x1E, 0x3A, 0x8A)
        elif line.startswith('## '):
            h2 = doc.add_heading(line[3:].strip(), level=2)
            h2.paragraph_format.space_before = Pt(12)
            h2.paragraph_format.space_after = Pt(4)
            for run in h2.runs:
                run.font.name = 'Calibri'
                run.font.size = Pt(14)
                run.font.bold = True
                run.font.color.rgb = RGBColor(0x1D, 0x4E, 0xD8)
        elif line.startswith('### '):
            h3 = doc.add_heading(line[4:].strip(), level=3)
            h3.paragraph_format.space_before = Pt(8)
            h3.paragraph_format.space_after = Pt(2)
            for run in h3.runs:
                run.font.name = 'Calibri'
                run.font.size = Pt(12)
                run.font.bold = True
                run.font.color.rgb = RGBColor(0x0F, 0x17, 0x2A)
        elif line.startswith('#### '):
            h4 = doc.add_heading(line[5:].strip(), level=4)
            h4.paragraph_format.space_before = Pt(6)
            h4.paragraph_format.space_after = Pt(2)
            for run in h4.runs:
                run.font.name = 'Calibri'
                run.font.size = Pt(11)
                run.font.bold = True
                run.font.color.rgb = RGBColor(0x33, 0x41, 0x55)
        elif line.startswith('> '):
            # Callout
            quote_text = line[2:].strip()
            tbl = doc.add_table(rows=1, cols=1)
            tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
            cell = tbl.cell(0, 0)
            set_cell_background(cell, "FEF3C7" if "[!WARNING]" in quote_text or "PENTING" in quote_text else "EFF6FF")
            set_cell_margins(cell, top=100, bottom=100, left=150, right=150)
            p = cell.paragraphs[0]
            p.paragraph_format.space_before = Pt(2)
            p.paragraph_format.space_after = Pt(2)
            clean_text = quote_text.replace('[!WARNING]', 'PERINGATAN:').replace('[!IMPORTANT]', 'PENTING:')
            run = p.add_run(clean_text)
            run.font.size = Pt(10)
            run.font.italic = True
            run.font.color.rgb = RGBColor(0x92, 0x40, 0x0E) if "PERINGATAN" in clean_text else RGBColor(0x1E, 0x40, 0xAF)
            doc.add_paragraph().paragraph_format.space_after = Pt(2)
        elif line.startswith('- [ ] ') or line.startswith('- [x] '):
            p = doc.add_paragraph()
            p.paragraph_format.space_before = Pt(1)
            p.paragraph_format.space_after = Pt(1)
            check = "☑ " if line.startswith('- [x] ') else "☐ "
            p.add_run(check).font.bold = True
            text_rest = line[6:].strip()
            # parse bold
            parts = re.split(r'(\*\*.*?\*\*)', text_rest)
            for part in parts:
                if part.startswith('**') and part.endswith('**'):
                    r = p.add_run(part[2:-2])
                    r.font.bold = True
                else:
                    p.add_run(part)
        elif line.startswith('- ') or line.startswith('* '):
            p = doc.add_paragraph(style='List Bullet')
            p.paragraph_format.space_before = Pt(1)
            p.paragraph_format.space_after = Pt(1)
            text_rest = line[2:].strip()
            parts = re.split(r'(\*\*.*?\*\*)', text_rest)
            for part in parts:
                if part.startswith('**') and part.endswith('**'):
                    r = p.add_run(part[2:-2])
                    r.font.bold = True
                else:
                    p.add_run(part)
        elif re.match(r'^\d+\.\s', line):
            m = re.match(r'^(\d+\.)\s*(.*)', line)
            p = doc.add_paragraph(style='List Number')
            p.paragraph_format.space_before = Pt(2)
            p.paragraph_format.space_after = Pt(2)
            text_rest = m.group(2).strip()
            parts = re.split(r'(\*\*.*?\*\*)', text_rest)
            for part in parts:
                if part.startswith('**') and part.endswith('**'):
                    r = p.add_run(part[2:-2])
                    r.font.bold = True
                else:
                    p.add_run(part)
        elif line.strip() == '---':
            p = doc.add_paragraph()
            p.paragraph_format.space_before = Pt(6)
            p.paragraph_format.space_after = Pt(6)
            r = p.add_run('_______________________________________________________________________________')
            r.font.color.rgb = RGBColor(0xD1, 0xD5, 0xDB)
            r.font.size = Pt(8)
        else:
            if line.strip():
                p = doc.add_paragraph()
                p.paragraph_format.space_before = Pt(2)
                p.paragraph_format.space_after = Pt(4)
                parts = re.split(r'(\*\*.*?\*\*|\`.*?\`)', line)
                for part in parts:
                    if part.startswith('**') and part.endswith('**'):
                        r = p.add_run(part[2:-2])
                        r.font.bold = True
                    elif part.startswith('`') and part.endswith('`'):
                        r = p.add_run(part[1:-1])
                        r.font.name = 'Consolas'
                        r.font.size = Pt(10)
                        r.font.color.rgb = RGBColor(0x9D, 0x17, 0x4D)
                    else:
                        p.add_run(part)

        i += 1

    if in_code_block:
        flush_code_block()
    if in_table:
        flush_table()

    doc.save(docx_path)
    print(f"Docx successfully created: {docx_path}")

if __name__ == '__main__':
    md_file = r'C:\Project\TUTORIAL_UBUNTU_ABSENSIKITA.md'
    docx_file = r'C:\Project\Tutorial_Ubuntu_AbsensiKita.docx'
    create_tutorial_docx(md_file, docx_file)
