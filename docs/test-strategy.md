# How to Test Monorepo Tools

In order to test the monorepo tools, one needs access to remote git repositories. These are hard (if not impossible) to
mock. Thus, the way to go is to provide a minimal set of sample repositories that are build on each start of the tests.

## Test Repositories

Two projects are created, ProjectA and ProjectB. For both projects bare repositories and working areas are created. The
projects will be initialized with two branches, `main` and `feature1` (ProjectA) /`feature2` (ProjectB) with different
content.

## Use Cases

Instead of unit tests we'll rely on acceptance tests for the `mono` commands. Each test case
performs a single usage scenario (use case).

### Use Case 1: Create a monorepo from existing subprojects

```Gherkin
Given subprojects 'ProjectA' and 'ProjectB' and the monorepo 'monorepo'
 When I work in 'monorepo'
  And I add the subprojects
 Then the files of the subprojects should be in their subdirectories
  And the monorepo config file should have been updated accordingly
```

### Use Case 2: Convert a subdirectory into a subproject

```Gherkin
Given a monorepo 'monorepo' with a file `lib/ProjectC/README.md`
 When I turn 'ProjectC' into a subproject
  And I clone 'ProjectC' into a new workspace
 Then 'ProjectC' should contain the README.md file
```

### Use Case 3: Update the monorepo only

Given

* Prepare subprojects
* Create and clone monorepo repository
* Add subprojects

When

* Add a file to the root of the workspace and commit it

Then

* The subprojects should not be affected

### Use Case 4: Update a subproject

Given

* Prepare subprojects
* Create and clone monorepo repository
* Add subprojects

When

* Add a file to directory ProjectA and commit it

Then

* The file should be in subproject ProjectA

### Use Case 5: Convert a subproject into a subdirectory

Given

* Prepare subprojects
* Create and clone monorepo repository
* Add subprojects

When

* Merge ProjectA
* Add a file to directory ProjectA and commit it

Then

* The file should not be in subproject ProjectA

## Directory Layout

```text
+ tests/
  + repos/
    + remote/
      + ProjectA.git/ 
      + ProjectB.git/ 
      + monorepo.git/
    + local/
      + ProjectA/ 
      + ProjectB/ 
      + monorepo/
```
