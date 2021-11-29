# Monorepo CLI

This tool is a partial replacement for git commands that utilizes `git-subtree` to manage monorepos.

## Installation

## Commands

-------------

### mono init

Initialize a fresh project. A local git repository is created with an empty initial commit. You do not need to
initialize projects that already are under git and have a clean working tree.

**Synopsis**

```bash
$ mono init
```

-------------

### mono add

Add (create, import) a subproject named `<name>` for the repository at `<url>` stored in `<directory>`.

**Synopsis**

```bash
$ mono add --dir=<directory> <name> <url>
```

-------------

### mono split

Turn the content of `<directory>` into a subproject named `<name>` with its own repository at `<url>`.

**Synopsis**

```bash
$ mono split --dir=<directory> <name> <url>
```

-------------

### mono rename

Rename the subproject named `<old>` to `<new>`. All configuration settings for the subproject are updated.

**Synopsis**

```bash
$ mono rename <old> <new>
```

-------------

### mono merge

Integrate the subproject named `<name>` into the main project. All configuration settings for the subproject are
removed.

**Synopsis**

```bash
$ mono merge <name>
```

-------------

### mono remove

Remove the subproject named `<name>`. All configuration settings for the subproject are removed.

**Synopsis**

```bash
$ mono remove <name>
```

-------------

### mono pull

Fetch and merge recent changes up to `<commit>` into the `<name>` subproject. This does not remove your own local
changes; it just merges those changes into the latest `<commit>`. With `--squash`, creates only one commit that contains
all the changes, rather than merging in the entire history.

If you use `--squash`, the merge direction does not always have to be forward; you can use this command to go back in
time from v2.5 to v2.4, for example. If your merge introduces a conflict, you can resolve it in the usual ways.

**Synopsis**

```bash
$ mono pull [--squash|--no-squash] <name> [<commit>]
```

-------------

### mono push

Push the subproject named `<name>` to the configured repository and `<ref>`. This can be used to push your subproject to
different branches of the remote repository.

**Synopsis**

```bash
$ mono push <name> [<ref>]
```

-------------
