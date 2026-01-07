# DOC/DOCX to PDF Converter Setup

This directory contains a Python script for converting Microsoft Word documents (DOC/DOCX) to PDF format using native Office applications.

## Requirements

- Python 3.6 or higher
- pip (Python package installer)
- **Windows**: Microsoft Word installed
- **Linux/macOS**: LibreOffice installed

## Installation

1. Install Python dependencies:
   ```bash
   pip install -r requirements.txt
   ```

   Or individually:
   ```bash
   pip install comtypes
   ```

2. **Install Office Software:**
   - **Windows**: Microsoft Word (part of Microsoft Office)
   - **Linux**: `sudo apt install libreoffice` (Ubuntu/Debian) or equivalent
   - **macOS**: Install LibreOffice from https://www.libreoffice.org

## Usage

The script is called automatically by the Laravel application when converting DOC/DOCX files.

Manual usage:
```bash
python docx_to_pdf.py input.docx output.pdf
python docx_to_pdf.py input.doc output.pdf
```

## Features

- **High-quality output** - Uses Microsoft Word/LibreOffice directly for perfect formatting
- **Perfect formatting preservation** - Maintains exact layout, fonts, and styling
- **Cross-platform** - Works on Windows (Word), Linux/macOS (LibreOffice)
- **Supports both DOC and DOCX** formats natively
- **Simple and reliable** - Direct Office application integration

## Dependencies

- `comtypes`: For controlling Microsoft Office applications on Windows

## Important Notes

- **Requires Office software**: Microsoft Word on Windows or LibreOffice on other platforms
- **Perfect fidelity**: Output PDFs exactly match the original document appearance
- **No watermarks**: Clean, professional output
- **Fast conversion**: Leverages native Office application performance

## Troubleshooting

If you encounter import errors, ensure all dependencies are installed:
```bash
pip list | grep comtypes
```

**System Requirements:**
- **Windows**: Microsoft Word must be installed and accessible
- **Linux/macOS**: LibreOffice must be installed

**Installation Commands:**
```bash
# Ubuntu/Debian Linux
sudo apt update && sudo apt install libreoffice

# CentOS/RHEL Linux
sudo yum install libreoffice

# macOS (using Homebrew)
brew install --cask libreoffice

# Windows: Install Microsoft Office or LibreOffice from:
# https://www.libreoffice.org/download/download/
```

**File Format Support:**
- Both .doc and .docx files are supported
- Direct conversion without intermediate steps

**Common Issues:**
- "comtypes not found": Install with `pip install comtypes`
- "Microsoft Word not found": Install Microsoft Word on Windows systems
- "LibreOffice not found": Install LibreOffice on Linux/macOS systems
- "Conversion failed": Ensure Office software is properly installed and accessible
- "Conversion failed": Ensure Office software is properly installed

**Performance Notes:**
- First conversion may be slower as Office application starts
- Subsequent conversions are much faster
- Large documents may take longer to process