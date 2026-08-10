---
name: Git push quirks
description: How to commit/push in this repl when the gitPush callback or shell git fails.
---
- Shell `git push` to GitHub always fails (auth: token not exposed to shell); shell `git commit` fails for missing identity unless run with `git -c user.name=... -c user.email=...`.
- The `gitPush` callback is the only working push path, but it can fail repeatedly with `CLI_ERROR / Failed to push branch: UNKNOWN` even when `git fetch origin` works.
**How to apply:** commit locally via shell with inline identity so work is safe, retry `gitPush` later; if it keeps failing, tell the user to push from the Git pane. Repo pack is ~665 MB — pushes are slow/timeout-prone.
