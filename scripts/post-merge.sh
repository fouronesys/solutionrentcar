#!/bin/bash
# Post-merge setup: keep it fast and idempotent.
# Backend is plain PHP (no build step). Mobile deps (mobile/node_modules)
# are tracked in git, so merges already bring dependencies with them.
set -e

echo "Post-merge setup OK: nothing to install or migrate."
