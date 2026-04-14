# Experiment Methodology For EduDev

This document provides a thesis-ready evaluation plan for EduDev, an integrated online learning platform for Mongolian learners. It is written as a reusable methodology template: replace bracketed placeholders such as `[N]`, `[Institution]`, or `[Date Range]` with the actual values used in your study.

## 1. Purpose

The purpose of the experiment is to evaluate whether EduDev is effective and usable as a Mongolian-language online learning platform that supports lesson delivery, assessment, learner progress monitoring, and certification in one system.

This methodology is intended to support academic reporting in a thesis, capstone project, conference paper, or technical report.

## 2. Evaluation Objectives

The experiment should verify the following:

- learners can access and complete lessons in the platform with minimal difficulty
- learners can complete tests and receive correct results within the same system
- learner progress is tracked consistently and visible through the dashboard
- certificates are generated correctly after successful completion of published exams
- the platform is understandable and usable for Mongolian-language users

## 3. Research Questions

Recommended research questions:

- `RQ1:` How usable is EduDev for Mongolian learners during common learning tasks?
- `RQ2:` How effectively does EduDev support lesson study, testing, progress tracking, and certification within one platform?
- `RQ3:` Does the system correctly record learner progress and exam outcomes during realistic use?
- `RQ4:` How satisfied are users with the platform interface, language support, and overall workflow?

## 4. Suggested Hypotheses

You can adapt the following hypotheses to match your final study design:

- `H1:` Participants will complete at least 85% of assigned tasks successfully.
- `H2:` The average System Usability Scale (SUS) score will be above 68, indicating acceptable usability.
- `H3:` The platform will correctly record learner progress, exam outcomes, and certificate issuance in all planned task scenarios.
- `H4:` Participants will report positive satisfaction with the Mongolian-language learning experience.

## 5. Study Design

A mixed-method evaluation is recommended.

### Quantitative component

- task completion rate
- time on task
- number of user errors
- post-task difficulty rating
- System Usability Scale (SUS) score
- optional usefulness or satisfaction rating on a 5-point Likert scale

### Qualitative component

- observation notes during task completion
- post-test comments
- short semi-structured interview
- feedback about language clarity, accessibility, and overall experience

### Recommended design type

A task-based usability study with direct observation is the most suitable baseline design. If the thesis also requires an effectiveness comparison, you may add one of these:

- a pre-test and post-test learning comparison
- a comparison between EduDev and an existing learning workflow
- a comparison between first-time and repeated use sessions

If no comparison group is required by your department, a single-group usability and effectiveness evaluation is sufficient for many undergraduate and master's projects.

## 6. Participants

### Recommended participant groups

Choose the participant structure that best matches your thesis scope:

- `Option A:` learner-only study
- `Option B:` learner study plus instructor or content-author study

### Recommended sample sizes

For a practical academic study:

- learners: `[15-30]` participants
- instructors or content authors: `[3-10]` participants if author-side evaluation is included

If the project is small in scope, at least `[10-15]` learner participants can still provide meaningful usability results, especially when combined with qualitative observations.

### Inclusion criteria

- participants are able to read Mongolian
- participants have basic computer or smartphone literacy
- participants belong to the intended learner group, such as university students, school students, or adult learners
- if instructor tasks are included, participants should have prior experience with teaching, tutoring, or content preparation

### Participant information to collect

- age range
- gender, if required by the ethics protocol
- education level
- prior experience with e-learning systems
- device used during the experiment
- self-reported digital skill level

## 7. Experimental Environment

Record the study environment clearly in the thesis:

- location: `[computer laboratory / classroom / remote online session]`
- date range: `[Date Range]`
- device type: `[desktop / laptop / mobile]`
- browser: `[Chrome / Edge / Firefox / other]`
- internet connection condition: `[stable / variable]`
- platform version: `[commit hash or release note]`

Before running the experiment, record the system verification status:

- application version or commit hash
- database state used for the experiment
- result of `php artisan test`

## 8. System Behaviors To Verify During The Study

The current implementation supports several academically important behaviors that can be checked directly during the experiment:

- exams are graded on the server
- exam time limits are enforced on the server
- certificates are issued per exam segment for successful published exams
- dashboard metrics are based on recorded learner activity
- exam interaction uses semantic controls suitable for keyboard and assistive-technology use
- empty text lessons cannot be published as complete learning materials

These points are useful when connecting the software implementation to the experiment design in your thesis.

## 9. Materials And Preparation

Prepare the following before the study:

- consent form
- participant information sheet
- demographic questionnaire
- test accounts for learners and authors
- at least one published lesson with content and an exam
- at least one lesson draft if author-side tasks are included
- observation sheet for the researcher
- post-task questionnaire
- SUS questionnaire
- interview guide

### Suggested lesson setup

For learner evaluation, prepare one realistic lesson that includes:

- at least one content segment
- at least one exam segment
- a visible dashboard outcome after progress is recorded
- a certificate outcome after a successful exam attempt

For author evaluation, prepare a clean account where the participant can:

- create a lesson
- add segments and content blocks
- add an exam with time limit and passing score
- publish the lesson

## 10. Task Scenarios

### Learner tasks

Use tasks like the following:

1. Sign in to the platform.
2. Change the interface language if requested.
3. Open an assigned lesson.
4. Read or watch the lesson content.
5. Continue until the system records learning progress.
6. Start the exam and answer all questions.
7. Submit the exam within the time limit.
8. Check the dashboard to confirm progress tracking.
9. If the exam is passed, open the certificate page and verify the issued certificate.

### Author or instructor tasks

Use these only if the author workflow is part of the evaluation:

1. Sign in to the platform.
2. Create a new lesson draft.
3. Add a title, lesson type, and lesson content.
4. Add at least one content segment.
5. Add an exam segment with questions.
6. Set the passing score and time limit.
7. Publish the lesson.
8. Re-open the lesson and verify that it is ready for learners.

## 11. Metrics

### Quantitative metrics

Use the following formulas or measurements:

- `Task completion rate = completed tasks / total assigned tasks x 100`
- `Average task time = total time for a task / number of participants`
- `Error count = total observable mistakes, failed attempts, or requests for help`
- `Post-task difficulty = participant rating on a 1-5 or 1-7 scale`
- `SUS score = standard SUS scoring method, 0-100 scale`

### System correctness checks

You may also record whether the platform behaved correctly during each critical workflow:

- progress updated after lesson interaction
- exam score matched submitted answers
- late submissions did not pass when the time limit was exceeded
- passed published exams produced certificates
- failed attempts did not produce certificates

### Suggested interpretation thresholds

- task completion rate above 85%: strong
- SUS score above 68: acceptable
- SUS score above 80: very good
- mean difficulty below 3 on a 5-point scale: favorable

## 12. Data Collection Instruments

### A. Observation sheet

Record:

- task start time
- task end time
- task success or failure
- number of visible errors
- whether assistance was needed
- notable user comments

### B. Post-task rating

After each task, ask:

- How easy or difficult was this task?
- Did anything confuse you?

Recommended scale:

- 1 = very easy
- 2 = easy
- 3 = neutral
- 4 = difficult
- 5 = very difficult

### C. System Usability Scale

Administer the standard 10 SUS items after all tasks are complete. Report the final mean SUS score and, if possible, the standard deviation.

### D. Short interview questions

Suggested questions:

- Which part of the system was easiest to use?
- Which part was most difficult or confusing?
- Was the Mongolian-language interface clear and natural?
- Did the dashboard and certificate features feel useful?
- What would you improve first?

## 13. Procedure

Use the following procedure for each session:

1. Welcome the participant and explain the purpose of the study.
2. Provide the consent form and participant information sheet.
3. Collect demographic background information.
4. Give a short neutral introduction to the platform without teaching the task answers.
5. Ask the participant to complete the assigned tasks while thinking aloud if that method is allowed.
6. Observe, time, and record each task.
7. Ask the participant to complete the post-task ratings and SUS form.
8. Conduct a short interview.
9. Thank the participant and store the collected data securely.

### Session duration

Recommended session length:

- learner session: `[20-35]` minutes
- author session: `[20-40]` minutes
- combined session: `[35-60]` minutes

## 14. Data Analysis

### Quantitative analysis

Report:

- participant count
- task completion percentage
- average task time per task
- average error count per task
- mean and standard deviation for SUS
- mean post-task difficulty rating

Use tables and charts where possible.

### Qualitative analysis

Group comments into themes such as:

- navigation clarity
- language clarity
- assessment usability
- perceived usefulness
- accessibility concerns
- improvement suggestions

Count recurring issues and identify high-impact usability problems.

## 15. Threats To Validity

Discuss limitations honestly in the thesis. Common examples:

- small sample size
- participants from only one institution or age group
- limited experiment duration
- novelty effect from first-time use
- use of prepared lessons instead of a large production course catalog

### Mitigation steps

- recruit participants with different experience levels
- use realistic tasks and learning content
- keep the testing environment consistent
- record all assistance given during tasks
- separate observed system issues from participant unfamiliarity

## 16. Ethical Considerations

Include the following if required by your institution:

- informed consent
- voluntary participation
- right to withdraw at any time
- anonymous or coded reporting of results
- secure storage of collected data
- no unnecessary collection of sensitive personal data

## 17. Reporting Template

You can structure the results chapter as follows:

### 17.1 Participant profile

| Metric | Value |
| --- | --- |
| Total participants | `[N]` |
| Learners | `[N]` |
| Authors or instructors | `[N]` |
| Average age | `[Value]` |
| Prior e-learning experience | `[Summary]` |

### 17.2 Task performance

| Task | Completion rate | Avg. time | Avg. errors | Notes |
| --- | --- | --- | --- | --- |
| Login and access lesson | `[ ]` | `[ ]` | `[ ]` | `[ ]` |
| Complete lesson content | `[ ]` | `[ ]` | `[ ]` | `[ ]` |
| Take exam | `[ ]` | `[ ]` | `[ ]` | `[ ]` |
| View dashboard progress | `[ ]` | `[ ]` | `[ ]` | `[ ]` |
| View certificate | `[ ]` | `[ ]` | `[ ]` | `[ ]` |

### 17.3 Usability summary

| Measure | Result |
| --- | --- |
| Mean SUS score | `[ ]` |
| SUS interpretation | `[acceptable / good / excellent]` |
| Mean difficulty rating | `[ ]` |
| Overall satisfaction | `[ ]` |

### 17.4 Qualitative themes

| Theme | Frequency | Example summary |
| --- | --- | --- |
| Navigation | `[ ]` | `[ ]` |
| Language clarity | `[ ]` | `[ ]` |
| Assessment flow | `[ ]` | `[ ]` |
| Dashboard usefulness | `[ ]` | `[ ]` |
| Improvement suggestions | `[ ]` | `[ ]` |

## 18. Suggested Thesis Wording

You can adapt the following paragraph:

> To evaluate the effectiveness and usability of the proposed EduDev platform, a task-based mixed-method experiment was conducted with `[N]` participants. The experiment measured task completion rate, task completion time, observable user errors, and System Usability Scale scores, while also collecting qualitative feedback through observation and short interviews. Participants completed realistic learning tasks involving lesson access, content study, examination, progress monitoring, and certificate retrieval. The collected data were analyzed using descriptive statistics and thematic grouping of participant feedback.

Replace the placeholders with your real study details before submission.

## 19. Recommended Evidence To Include In The Thesis Appendix

- task sheet
- consent form
- demographic questionnaire
- SUS questionnaire
- raw task results table
- screenshots of lesson view, exam view, dashboard, and certificate page
- system test result summary from the same version used in the experiment

## 20. Final Note

This methodology document strengthens the academic readiness of the project, but it does not replace the actual experiment. The final thesis should report the real participant sample, real measurements, real findings, and a discussion of limitations based on the completed study.
