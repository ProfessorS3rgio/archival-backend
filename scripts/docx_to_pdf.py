#!/usr/bin/env python3
"""
DOC/DOCX to PDF Converter using comtypes (Windows) or LibreOffice (Linux)
Converts Microsoft Word documents to PDF format using native Office applications
Supports both .doc and .docx files with high-quality output
"""

import sys
import os
import subprocess
import platform
import shutil

def convert_word_to_pdf_windows(input_path, output_path):
    """
    Convert Word document (.doc or .docx) to PDF using comtypes on Windows

    Args:
        input_path (str): Path to input Word document
        output_path (str): Path for output PDF file

    Returns:
        bool: True if conversion successful, False otherwise
    """
    try:
        import comtypes.client

        # Create Word application object
        word = comtypes.client.CreateObject('Word.Application')
        word.Visible = False

        # Open the document
        doc = word.Documents.Open(input_path)

        # Save as PDF (FileFormat=17 for PDF)
        doc.SaveAs(output_path, FileFormat=17)

        # Close document and quit Word
        doc.Close()
        word.Quit()

        return True

    except Exception as e:
        print(f"Windows conversion failed: {e}", file=sys.stderr)
        return False

def convert_word_to_pdf_linux(input_path, output_path):
    try:
        libreoffice_paths = [
            '/usr/bin/libreoffice',
            '/usr/bin/soffice',
            '/opt/libreoffice/program/soffice'
        ]

        libreoffice_cmd = next((p for p in libreoffice_paths if os.path.exists(p)), None)

        if not libreoffice_cmd:
            print("LibreOffice not found.", file=sys.stderr)
            return False

        # Stable, deterministic environment
        env = os.environ.copy()
        profile_dir = '/var/lib/libreoffice-profile'
        os.makedirs(profile_dir, exist_ok=True)

        env.update({
            'HOME': '/tmp',
            'XDG_CONFIG_HOME': '/tmp',
            'XDG_CACHE_HOME': '/tmp',
            'DISPLAY': '',
            'LANG': 'en_US.UTF-8',
            'LC_ALL': 'en_US.UTF-8',
        })

        outdir = os.path.dirname(output_path) or '.'

        result = subprocess.run([
            libreoffice_cmd,
            '--headless',
            '--nologo',
            '--nofirststartwizard',
            f'-env:UserInstallation=file://{profile_dir}',
            '--convert-to',
            (
                'pdf:writer_pdf_Export:'
                'EmbedStandardFonts=true,'
                'UseLosslessCompression=true,'
                'ReduceImageResolution=false,'
                'SelectPdfVersion=1'
            ),
            '--outdir', outdir,
            input_path
        ], capture_output=True, text=True, timeout=300, env=env)

        if result.returncode != 0:
            print(f"LibreOffice error: {result.stderr}", file=sys.stderr)
            return False

        produced_pdf = os.path.join(
            outdir,
            os.path.splitext(os.path.basename(input_path))[0] + '.pdf'
        )

        if not os.path.exists(produced_pdf):
            print(f"PDF not produced: {produced_pdf}", file=sys.stderr)
            return False

        if os.path.abspath(produced_pdf) != os.path.abspath(output_path):
            os.makedirs(os.path.dirname(output_path) or '.', exist_ok=True)
            if os.path.exists(output_path):
                os.remove(output_path)
            shutil.move(produced_pdf, output_path)

        return True

    except subprocess.TimeoutExpired:
        print("LibreOffice conversion timed out", file=sys.stderr)
        return False
    except Exception as e:
        print(f"Linux conversion failed: {e}", file=sys.stderr)
        return False

def convert_word_to_pdf(input_path, output_path):
    """
    Convert Word document (.doc or .docx) to PDF

    Args:
        input_path (str): Path to input Word document
        output_path (str): Path for output PDF file

    Returns:
        bool: True if conversion successful, False otherwise
    """
    try:
        # Check if input file exists
        if not os.path.exists(input_path):
            print(f"Input file does not exist: {input_path}", file=sys.stderr)
            return False

        # Check file extension
        file_ext = os.path.splitext(input_path)[1].lower()
        if file_ext not in ['.doc', '.docx']:
            print(f"Unsupported file type: {file_ext}. Only .doc and .docx files are supported.", file=sys.stderr)
            return False

        # Determine platform and use appropriate conversion method
        current_platform = platform.system().lower()

        if current_platform == "windows":
            success = convert_word_to_pdf_windows(input_path, output_path)
        elif current_platform in ["linux", "darwin"]:  # Linux or macOS
            success = convert_word_to_pdf_linux(input_path, output_path)
        else:
            print(f"Unsupported platform: {current_platform}", file=sys.stderr)
            return False

        if success and os.path.exists(output_path):
            print(f"Successfully converted {input_path} to {output_path}")
            return True
        else:
            print(f"Conversion failed: output file was not created", file=sys.stderr)
            return False

    except Exception as e:
        error_msg = str(e) if str(e) else "Unknown error occurred"
        print(f"Conversion failed: {error_msg}", file=sys.stderr)
        return False

def main():
    if len(sys.argv) != 3:
        print("Usage: python docx_to_pdf.py <input.doc[x]> <output.pdf>", file=sys.stderr)
        sys.exit(1)

    input_path = sys.argv[1]
    output_path = sys.argv[2]

    success = convert_word_to_pdf(input_path, output_path)
    sys.exit(0 if success else 1)

if __name__ == "__main__":
    main()