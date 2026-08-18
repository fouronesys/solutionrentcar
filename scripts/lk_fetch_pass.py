#!/usr/bin/env python3
"""
Reads /tmp/lkrentacar_db.php downloaded via FTP and reports the current
$this->pass assignment: quote style, actual length, masked value.
Exits with code 1 if the assignment is not found.
"""
import re
import sys

content = open('/tmp/lkrentacar_db.php').read()

# Escape-aware: single-quoted (only \\ and \' are escapes) or double-quoted
pattern = r"""\$this->pass\s*=\s*(?:'((?:[^'\\]|\\.)*)'|"((?:[^"\\]|\\.)*)")"""
m = re.search(pattern, content)
if not m:
    sys.stderr.write("WARNING: no $this->pass assignment found\n")
    sys.exit(1)

sq_raw, dq_raw = m.group(1), m.group(2)
if sq_raw is not None:
    q = "'"
    pw_actual = re.sub(r"\\([\\'])", lambda mm: mm.group(1), sq_raw)
else:
    q = '"'
    pw_actual = dq_raw  # conservative: report raw length

stars = '*' * len(pw_actual)
print("quote style: " + repr(q))
print("password length (actual chars): " + str(len(pw_actual)))
print("$this->pass = " + q + stars + q + ";")
