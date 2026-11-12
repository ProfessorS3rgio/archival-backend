#!/usr/bin/env python3
"""Merge PDF paths in order into one output PDF."""

from pathlib import Path
import sys

from pypdf import PdfWriter


def main() -> int:
    if len(sys.argv) < 4:
        print("Usage: merge_pdfs.py OUTPUT INPUT...", file=sys.stderr)
        return 2

    output = Path(sys.argv[1])
    inputs = [Path(path) for path in sys.argv[2:]]
    writer = PdfWriter()

    try:
        for input_path in inputs:
            if not input_path.is_file():
                raise FileNotFoundError(str(input_path))
            writer.append(str(input_path))

        with output.open("wb") as stream:
            writer.write(stream)
    except Exception as error:
        print(f"PDF merge failed: {error}", file=sys.stderr)
        return 1

    return 0


if __name__ == "__main__":
    raise SystemExit(main())
