# Functional Requirements
## Proof of Concept
### 1. GIT
 - FR 1.1 [x] Load git repository from web.
 - FR 1.2 [x] Load the complete git history.
 - FR 1.3 [x] Display each commit from history.
 - FR 1.4 [x] Display the list of issues in each commit.
 - FR 1.5 [x] Load new commits as they are created.
 - FR 1.6 [x] Open GitHub commit view for each commit.
### 2. News(Code)Feed
 - FR 2.1 [x] For each loaded commit, generate a few newsfeed posts that explain what was done.
 - FR 2.2 [x] Apply ranking algorithm to each post.
 - FR 2.3 [x] Display each post in sorted order according to the ranking algorithm.
### 3. Commit Analysis
 - FR 3.1 [x] Use LLM to infer the summary of each commit.
 - FR 3.2 [x] Use LLM to infer bugs and security issues in each commit.

## MVP
### 1. GIT
 - FR 1.1 For a commit, find code that was changed by it and load authors.
### 2. News(Code)Feed
 - FR 2.1 Create posts that convey that somebody changed user's code.
 - FR 2.2 Create posts that say that somebody removed user's code.
 - FR 2.3 Personalize news feed for the current user.
 - Implement "endless" scrolling in CodeFeed UI.
### 3. Commit Analysis
 - FR 3.1 FR 2.3 Find additional features for ranking:
     - Bugs
     - Security Issues
     - Code Complexity
     - Size of code
 - FR 3.2 Use LLM to infer bugs and security issues in each commit.
### 4. Task Management
 - Implement task assignment directly from the CodeFeed UI.
### 5. User management
 - FR 5.1 Implement user login and registration.