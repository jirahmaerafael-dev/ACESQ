
ACES TAGUM COLLEGE, INC.
Mankilam, Tagum City
Bachelor of Science in Information Technology

ACESQ: A Simple Campus Office Queuing System

A Project Documentation Submitted in Partial Fulfillment
of the Requirements for
CC106 – Application Development

Submitted by:
Rafael, Jirah Mae O.
 Benjamin, John Mark
Gingoyon, Jeds Kyle G.
Cabaral, Norhia N.

Submitted to:
Boston, Delf

A.Y. 2026–2027

TABLE OF CONTENTS
STANDARD DOCUMENT FORMAT  ………………………………………………...6
1.1 Background of the Project  ……………………………………………………..7
1.2 Statement of the Problem  ……………………………………………………...8
1.3 Objectives of the Project  ……………………………………………………….9
1.4 Scope and Limitation   …………………………………………………………10
1.5 Target Users / Beneficiaries …………………………………………………..11
1.6 Related Systems  ………………………………………………………………12
1.7 Definition of Terms  …………………………………………………………….	13
2.1 Development Methodology  …………………………………………………..	15
2.2 Requirements Gathering  ……………………………………………………..	16
2.3 Functional Requirements  …………………………………………………….	17
2.4 Non-Functional Requirements  ……………………………………………….	18
2.5 System Architecture  …………………………………………………………..	19
2.6 Use Case Diagram  ……………………………………………………………	20
2.7 Database Design / ERD  ………………………………………………………	21
2.8 User Interface Design  ………………………………………………………...	22
2.9 Development Tools and Technologies  ………………………………………	25
2.10 Testing Methodology  ………………………………………………………...	26
2.11 Project Timeline  ………………………………………………………………	27
3.1 System Overview  ……………………………………………………………..	29
3.2 Final System Architecture  ……………………………………………………	30
3.3 Final Database Design  ……………………………………………………….	31
3.4 Data Dictionary  ………………………………………………………………..	32
3.5 System Modules  ………………………………………………………………	35
3.6 User Roles and Permissions  …………………………………………………	37
3.7 System Workflow  ……………………………………………………………...	38
3.8 Implemented User Interfaces  ………………………………………………..	40
4.1 Functional Testing Results  …………………………………………………...	48
4.2 Integration Testing  …………………………………………………………….	50
4.3 User Acceptance Testing  ……………………………………………………..	52
4.4 Issues Encountered and Solutions Applied  ………………………………...	53
4.5 Summary of Results  …………………………………………………………..	54
5.1 Conclusion  ……………………………………………………………………..	56
5.2 Recommendations  …………………………………………………………….	57
5.3  References  ……………………………………………………………………	58
APPENDICES  ………………………………………………………………………...	59
APPENDIX A – USER MANUAL  ………………………………………………...	59
APPENDIX B – INSTALLATION / DEPLOYMENT GUIDE  ……………………	61
APPENDIX C – TESTING FORMS / UAT FORMS  …………………………….	65
APPENDIX D – GITHUB REPOSITORY AND GROUP CONTRIBUTION  ….	68
APPENDIX E – OTHER SUPPORTING DOCUMENTS  ………………………	68
FINAL DOCUMENTATION CHECKLIST  …………………………………………..	69

LIST OF TABLES
Use References > Insert Caption and select the label "Table" for every table. Then press Ctrl+A and F9 to update this list. 
No table of figures entries found.


LIST OF FIGURES
Use References > Insert Caption and select the label "Figure" for every diagram, screenshot, chart, or image. Then press Ctrl+A and F9 to update this list.
No table of figures entries found.

STANDARD DOCUMENT FORMAT
Apply these rules consistently to the final documentation.
Item
Required Format
Paper Size
Letter / Short Bond Paper (8.5 × 11 inches)
Margins
Left: 1.5 in; Top: 1 in; Right: 1 in; Bottom: 1 in
Font
Arial
Body Text Size
12 pt
Line Spacing
1.5
Alignment
Justified for body paragraphs
First-Line Indent
0.5 inch
Chapter Titles
14 pt, bold, ALL CAPS, centered
Section Headings
12 pt, bold, left aligned
Page Number
Footer, right side
Figures
Readable, centered, caption below
Tables
Readable, with a consistent title and header row
Submission
DOCX and PDF unless otherwise instructed


1.1 Background of the Project
The Registrar's Office assists students with many school-related concerns, and during enrollment and other busy periods, a large number of students need its services at the same time. Because students are served through separate windows, such as Registrar, Cashier, Window 3, Window 4, and Window 5, long lines, crowded waiting areas, and confusion about who will be served next are common problems. Waiting in a long line can be tiring and inconvenient, especially for students who have classes or other responsibilities. A study at City College of Angeles found that some students waited as long as 2 hours and 47 minutes at the Registrar's Office during enrollment [1].
Technology can make the queuing process easier and more organized. Espinosa, Serrano, and Balmes developed an automated enrollment queueing system that aimed to improve student waiting lines, reduce overcrowding, lessen the stress of staying in crowded areas, and reduce the workload of employees [2]. Research on electronic queue management systems likewise shows that technology can improve service processes, reduce waiting times, and raise the overall quality of service [3].
To address these concerns, ACESQ (A Simple Campus Office Queuing System) is a web-based application developed for queue management at ACES Tagum College. A student selects an active service window, enters a name, reads and accepts the queue terms, and receives a ticket. Each window uses its own ticket prefix and daily number sequence. Students can open their ticket page to see its status, current position, the ticket being served, and an estimated wait. The page refreshes queue information every eight seconds. Students do not create accounts, but the system stores the entered name with the queue record and uses a hashed browser-session identifier when checking for another active ticket.
The system also manages missed calls. When staff call a ticket, a five-minute grace period begins. If the student is not marked present before the grace period expires, the first missed call moves the ticket back five places, the second moves it back ten places, and a third missed call forfeits it. The staff dashboard supports calling the next ticket, marking a student present, and completing a transaction. A separate administrator area manages windows and staff accounts and provides queue monitoring, missed-call history, and date-filtered queue analytics.
 Through these functions, ACESQ aims to provide a simple, organized, and convenient way of managing registrar queues while reducing unnecessary physical lines and crowded waiting areas.
1.2 Statement of the Problem
Registrar windows at ACES Tagum College experience long lines and crowded waiting areas, especially during enrollment and other peak periods. Students are often required to wait physically in line without knowing how long it will take before they are served, and there is no organized way of handling students who are called but are not present.
Specifically, the project aims to address the following problems:
1. Students have no way to know their queue position or estimated waiting time without being physically present in line.
2. Long lines and crowded waiting areas form at the registrar windows during enrollment and other peak periods.
3. Students who miss their turn because of a short, reasonable delay may lose their place in the queue entirely.
4. Staff have to handle absent students manually, which can be inconsistent and takes time away from serving other students.
5. Manual or semi-manual processes make it difficult to monitor how many students are waiting at each window and to track the status of each transaction.



1.3 Objectives of the Project
The objective of this project was to develop ACESQ, a web-based queue-management system for ACES Tagum College that lets students obtain and monitor tickets, supports staff in serving queues, and provides administrators with tools to manage service windows, staff accounts, and queue history.

Specific Objectives
Specifically, the project aims to:
1. Allow students to select an active service window and enter the name staff can use when calling them, without creating an account;
2. Require students to accept the queue terms before a ticket is issued;
3. Issue sequential daily ticket numbers independently for each window, using a distinct prefix for each window;
4. Prevent a student with an active ticket from receiving another ticket, and provide live status, position, now-serving number, estimated wait, and a call countdown;
5. Apply the five-minute grace period and escalating missed-call policy automatically: five-place pushback after the first miss, ten-place pushback after the second, and forfeiture after the third;
6. Provide staff with window-scoped queue controls and provide administrators with separate pages to manage windows and accounts, monitor queues, and review queue history.








1.4 Scope and Limitations
ACESQ supports student queueing for the configured service windows at ACES Tagum College. Students can view active windows, enter their name, accept the terms, and receive a ticket without logging in. Each window has its own sequential number series and prefix (for example, R001 or C001). The system stores each ticket with its date, status, student name, and a snapshot of its prefix. A student with an active ticket is redirected to that ticket instead of receiving another one; this check uses the normalized name and the current browser session.
The student ticket page displays the ticket number, service window, status, queue position, current ticket being served, estimated waiting time, and a countdown after the ticket is called. It refreshes every eight seconds. Staff can manage only their assigned window and can call the next waiting ticket, mark the called student present, and complete the transaction. Administrators can manage service windows and staff accounts, view current-day queues and missed calls, and filter queue history by date and optional window. The volume chart compares all windows for the selected date.

Limitations
Only staff are required to log in. Students do not have accounts, so ACESQ does not store personal student profiles or transaction history. The system does not send push notifications, SMS, or email alerts when a student's number is nearing; students must check the queue status page themselves within their browser. It does not include a payment processing feature, as it is limited to queue management only. ACESQ is designed for single-campus use and does not support the coordination of queues across multiple campuses or branches.
The system also relies on students having access to a smartphone, tablet, or computer with internet access in order to obtain and monitor their queue number, and it does not eliminate the need for an on-site option for students without such access. The escalating pushback feature applies only within a single day's queue, as queue numbers and penalty counts reset daily and do not carry over.



1.5 Target Users / Beneficiaries
The primary users of ACESQ are students, registrar service-window staff, and administrators. 
Students
Students are the primary users of ACESQ. They benefit by being able to get a queue number online without logging in, monitor their position remotely, and avoid standing in a physical line for an extended period. The escalating pushback feature also benefits students directly, since a short, reasonable delay no longer costs them their place in line entirely.
Registrar Staff
Registrar staff assigned to the windows are the secondary users. They benefit from a centralized dashboard that lets them call numbers and track the waiting list, while the system handles absent students automatically. This reduces the manual effort of organizing a physical queue and handling disputes over turns.
Administrators
Administrators manage the queueing service and review its activity. The administrator area provides separate pages for the dashboard, service windows, staff accounts, today's queues, missed calls, and queue history and analytics.




1.6 Related Systems
Several studies and existing systems have been developed to help organizations manage waiting lines and improve the quality of service. These are related to the proposed ACESQ system.
City College of Angeles Registrar Queueing Study. This related study documented the long waiting times experienced by students at the Registrar's Office during enrollment, with waits of up to 2 hours and 47 minutes recorded. It highlighted the real-world problem that ACESQ aims to address, though it focused on documenting the issue rather than providing a software solution [1].
Automated Enrollment Queueing System (Espinosa, Serrano, & Balmes). This system was developed to improve student waiting lines during enrollment, reduce overcrowding, and lessen the workload of employees. It is similar to ACESQ in its goal of automating the queuing process, but it does not appear to include a no-show management or pushback mechanism for students who miss their turn [2].
Electronic Queue Management Systems. Broader research on electronic queue management systems shows that queuing technology improves service processes, reduces waiting times, and raises the overall quality of service in various settings, not limited to schools. This supports the general direction of ACESQ but does not propose the specific escalating pushback penalty feature that this study introduces [3].
Common commercial queuing systems. Systems used in banks, government offices such as the SSS or NBI, and hospitals typically use a "take a number and wait" model with a physical kiosk and a display screen. These are effective for in-person waiting but usually do not allow remote queue status monitoring through a personal device, and typically do not include an automated, escalating penalty for absent customers.
Compared with these existing systems, ACESQ focuses on the basic queuing needs of the registrar windows. Students choose a window and get a number without logging in, can monitor the queue from their own device, and are treated fairly through an automatic pushback penalty that does not require staff decisions. Unlike broader queue management systems, the proposed system maintains a focused scope centered on window-based queuing and no-show handling.


Definition of Terms

ACESQ – the name of the proposed system, short for "A Simple Campus Office Queuing System," developed for ACES Tagum College.
Service Window – a service counter of the Registrar's Office (e.g., Window 3, Window 4, Window 5) that serves students and maintains its own queue.
Ticket Number– The displayed identifier for a queue record, formed from the window prefix and a zero-padded daily sequence (for example, R001).
Queue Position – The current place of a waiting ticket in its window's waiting list. A ticket's position may change after a missed-call pushback.
Grace Period – the set amount of time (e.g., 5 minutes) given to a student to present themselves after their number has been called, before a no-show penalty is applied.
No-Show – a status applied when a student's number is called but the student fails to appear within the grace period.
Pushback Penalty – the system's method of moving an absent student's number back a set number of slots in the queue, rather than removing it entirely, as a consequence for a missed call.
Escalating Penalty – the increasing severity of the pushback penalty for repeated absences (five slots on the first miss, ten slots on the second).
Forfeiture – the cancellation of a student's queue number after the third missed call, requiring the student to get a new number.
Staff Dashboard – the interface used by authorized registrar personnel to manage the queue of their window, call numbers, and update student statuses.
Terms and Conditions – the set of rules a student agrees to before receiving a queue number, including the grace period and pushback policy.
Web-Based System – a system accessible through a web browser, without requiring installation of a dedicated mobile or desktop application.
Administrator Dashboard - The administrator interface for managing windows and staff accounts and viewing queue monitoring, history, and analytics pages.
Active Ticket - A ticket with a waiting, called, or present status for the current queue date. A request for another ticket is redirected to it.
Queue History and Analytics - The administrator page that filters queue records by date and optional service window, summarizes statuses, and compares window volume for the selected date

CHAPTER 2 – METHODOLOGY
2.1 Development Methodology
The project used an iterative Agile approach, described by the group as a simplified Scrum-like process suited to a small student team and a fixed academic schedule. Work was divided into increments so that queue issuance, student status viewing, staff controls, missed-call handling, and administrator pages could be implemented and refined in stages.
During planning, the group identified the queue-management problem and defined requirements for student ticket issuance, window-specific numbering, duplicate-ticket prevention, live status, staff actions, missed-call handling, and administrator functions.
During design, the group prepared the application architecture, database schema, queue workflow, and user-interface designs. The design included distinct staff and administrator roles and separate administrator pages.
During development, the team implemented the student queue flow, ticket status page, staff dashboard, missed-call rules, staff and administrator access, window and staff management, and queue-history analytics.
Testing was performed throughout development. Automated Laravel feature tests exercised application requests, queue operations, access restrictions, validation, and history filters. Chapter 4 reports the available automated results separately from user acceptance testing.
During review and refinement, the queue rules and interfaces were adjusted as the project developed. The final missed-call behavior uses a five-minute grace period, a five-place pushback after the first miss, a ten-place pushback after the second, and forfeiture after the third.
An iterative approach was appropriate because queue handling and administrator requirements evolved during implementation. This report describes delivered behavior rather than treating planned features as completed work.


2.2 Requirements Gathering
The group used observation, review of related literature, and informal consultation with students to gather the requirements for ACESQ.
The group observed how students currently queue at the registrar windows during peak periods such as enrollment, noting problems such as long physical lines and the lack of a clear process for handling students who are called but are not present.
The group also reviewed related studies and existing queueing solutions (see Section 1.6, Related Systems) to identify gaps that ACESQ could address, particularly the absence of an automated, fair no-show policy in existing systems.
Lastly, the group gathered informal feedback from fellow students about their experiences and frustrations with current queuing practices, which helped confirm the need for remote queue monitoring. Based on these findings, the group decided that students should only choose a window and agree to the terms and conditions, without logging in, so that getting a queue number stays quick and simple.











2.3 Functional Requirements
MODULE 
FUNCTIONAL REQUIREMENTS
1. Window Selection
The system shall display active service windows and their ticket prefixes; inactive windows shall not accept new tickets.
2. Terms and Conditions
The system shall require students to read and agree to the terms and conditions, including the grace period and pushback policy, before receiving a queue number.
3.  Student details
The system shall require a student name before issuing a ticket and associate the ticket with the current browser-session hash.
4. Ticket numbering
The system shall issue sequential numbers independently per window and queue date, display each number with its window prefix, and retain the prefix on the issued ticket.
5. Duplicate prevention
The system shall direct a student with a waiting, called, or present ticket to that existing ticket instead of issuing another active ticket.
6. Live ticket status
The system shall display ticket status, position, now-serving ticket, estimated wait, and call countdown; the page shall refresh status every eight seconds.
7. Staff authentication and scope
The system shall require staff to authenticate and restrict staff queue actions to their assigned service window.
8.  Queue controls
The system shall allow assigned staff to call the next waiting ticket, mark the called student present, and mark the transaction completed.
9. Grace period
The system shall start a five-minute grace period when a ticket is called.
10.Missed-call handling
After an expired grace period without a present status, the system shall record a missed call, push the ticket back five places on the first miss and ten on the second, and forfeit it on the third.
11. Window management
The system shall allow administrators to add, update, activate, and deactivate service windows with unique prefixes.
12. Staff-account management
The system shall allow administrators to create and manage staff and administrator accounts and assign staff to windows.
13. Monitoring and analytics
The system shall provide administrator pages for today's queues, missed calls, and date-filtered ticket history with status totals and a per-window volume chart.


2.4 Non-Functional Requirements
The non-functional requirements explain how ACESQ should work and what users can expect from the system. For security, passwords are stored in a protected form, and only authorized staff and administrators can access their assigned pages. Staff can only manage their assigned window, while students can view their tickets using a unique public token. For usability, students can get and check their queue tickets through a web browser without creating an account. The system is also designed to work on both desktop computers and mobile phones.
For timeliness, the student page automatically updates the ticket status every eight seconds. The waiting time shown is only an estimate and is not guaranteed. For reliability, the system checks expired or missed calls every minute using the Laravel scheduler. It also checks expired calls whenever queue activities happen. The scheduler must be running for automatic checks to work properly. For compatibility, ACESQ works with common web browsers and does not require a separate mobile application. Lastly, database backups, data storage, and system monitoring must be properly set up during deployment because these are not included as built-in features of the system.






2.5 System Architecture

Figure 1. ACESQ System Architecture Diagram

ACESQ uses a three-tier web architecture: a browser-based presentation layer, a Laravel application layer, and a MySQL database layer.
Presentation layer: Blade-rendered pages, HTML, Tailwind CSS, and JavaScript provide the student window list, terms and ticket form, live ticket page, staff dashboard, and separate administrator pages. The live ticket page requests status data every eight seconds.
Application layer: Laravel routes, middleware, controllers, models, and queue services validate requests, enforce staff and administrator access, issue tickets, calculate queue status, update staff actions, and apply missed-call rules. A scheduled command checks expired calls every minute; queue requests also trigger expiry checks.
Database layer: MySQL stores service windows, staff accounts, queue records, and missed-call events. Database constraints support unique window prefixes, daily per-window ticket numbers, unique public tokens, and queue-to-window and missed-call-to-ticket relationships.

2.6 Use Case Diagram
The use case diagram should show three actors: Student, Service-window Staff, and Administrator. Students select a window, enter their name, accept the terms, receive a ticket, and monitor its status. Staff log in to their assigned window, call the next ticket, mark a student present, and complete a transaction. Administrators manage windows and staff accounts and review current queues, missed calls, and date-filtered analytics. The scheduled application process checks expired calls and applies the missed-call policy.




Figure 2. Use case diagram of ACESQ. 

2.7Database Design / ERD
The implemented database contains four main tables: windows, staff_users, queue_records, and missed_calls. Windows are related to staff accounts and queue records. Queue records are related to missed-call events. Students do not have account records; a queue record contains the entered student name and a nullable hash of the browser session identifier used for duplicate-active-ticket detection. Section 3.4 lists the fields and key relationships.


 			Figure 3. Entity relationship diagram of ACESQ 

2.8 User Interface Design
The interface uses a consistent, responsive design across student, staff, and administrator pages. Student screens focus on window selection, name entry and terms, and a high-visibility ticket/status display. Staff screens prioritize the current ticket, queue actions, waiting list, and missed-call history. The administrator interface uses left-side navigation and separate pages for the dashboard, service windows, staff accounts, today's queues, missed calls, and queue history and analytics. 

Figure 4. ACESQ student dashboard


Figure 5. ACESQ staff & admin Log in



Figure 6. ACESQ Admin dashboard

Figure 7. ACESQ Staff dashboard

2.9 Development Tools and Technologies

Tool / technology
Use in ACESQ
PHP 8.5
Server-side programming language.
Laravel 13.38
Framework for routing, authentication, validation, queue logic, and database access.
MySQL
Configured application database for windows, staff accounts, queue records, and missed-call events.
Blade, HTML, JavaScript
Server-rendered templates, page structure, and live student-status refresh.
Tailwind CSS 4.3
Responsive styling for student, staff, and administrator interfaces.
Visual Studio Code
Code editor used for application development.
Laravel feature testing / PHPUnit
Automated verification of queue workflows, authorization, validation, and analytics.








2.10 Testing Methodology
The testing methodology for ACESQ aims to ensure that the system functions correctly, efficiently, and reliably for students and campus office staff. Testing will be conducted to verify that each module performs its intended function and that the system meets the requirements of the project.
The researchers will use the following testing methods:
Unit Testing - Each module of the system will be tested individually to ensure that its functions work properly. This includes queue registration, queue number generation, queue status monitoring, no-show management, and staff dashboard operation.
Integration Testing - This will verify that the different modules work together correctly. For example, when staff call the next queue number, the student's queue position should update accordingly, and the system should apply the appropriate pushback penalty if a student misses a call.
Functional Testing - The researchers will check whether the system meets its specified requirements. This includes verifying online queue registration, office selection, queue number generation, queue status updates, grace period countdowns, and automatic forfeiture after three missed calls.
Usability Testing - Selected students and campus office staff will evaluate whether the system is easy to understand and navigate using computers or mobile devices. Their feedback will help identify difficulties in using the system.
Error Handling and Validation Testing - The system will be tested using invalid inputs, repeated registrations, unauthorized staff access, and other possible errors to determine whether it responds appropriately and prevents incorrect queue operations.

2.11 Project Timeline


The project timeline presents the planned and ongoing activities for the development of ACESQ: Student Queue Management System, which started on October 8, 2026, with a target completion date of November 20, 2026. The initial activities included project planning and gathering system requirements to identify the needs of ACES Tagum College. These activities were followed by system and user interface design, where the layout and features of the application were planned, and database design, which focused on organizing the information needed for the queueing system.
The succeeding activities included system development, functional testing, and integration testing. During development, the researchers worked on the student ticketing process, staff dashboard, administrator features, and queue management functions. Testing was conducted to check the system's features, identify errors, and ensure that the different modules worked properly. User Acceptance Testing (UAT) was also included to evaluate whether the system meets the needs of its intended users. Documentation was updated throughout the development process to record the activities, progress, and necessary revisions. The timeline reflects the project's activities from October 8 to November 20, 2026, including completed, ongoing, and pending tasks.



CHAPTER 3 – SYSTEM DEVELOPMENT AND IMPLEMENTATION
3.1 System Overview
ACESQ is an implemented web application for student queueing and service-window operations at ACES Tagum College. Students select an active window, enter their name, accept the terms, and receive a prefixed daily ticket. They can monitor ticket status from a personal device without creating an account. Staff sign in to a window-scoped dashboard. Administrators use a separate left-navigation area to manage windows and accounts and review queue activity.
A student cannot receive another ticket while an active ticket is associated with the same normalized name or browser session. The application opens the existing ticket. Queue numbers are independent per window and date; a ticket retains the prefix assigned when it was issued. make it like this ACESQ is a web-based application designed to improve the queuing process at ACES Tagum College. It aims to reduce long physical lines, organize student transactions, and help campus office staff manage queues more efficiently.
The system allows students - To select a campus office, agree to the terms and conditions, obtain a queue number online, and monitor their queue position through a web browser. Students can also view the number currently being served and check the grace period countdown when their number is called.
For campus office staff - ACESQ provides a dashboard for managing the waiting list, calling the next student, updating queue statuses, and monitoring no-show records. The system also implements an automated no-show management feature with an escalating pushback penalty. When a student misses the first call, their queue number is moved back five slots.

ACESQ supports multiple campus offices including the Registrar, Cashier, Enrollment Office, and Student Services. Each office maintains its own independent queue. The system is accessible through computers and mobile devices using a web browser without requiring a separate application installation
 
3.2 System Architecture
The final application uses the three-tier architecture discussed in Section 2.5. Web browsers display the pages for students, staff, and administrators. Laravel handles and checks user requests, applies the queue rules, and connects to the MySQL database. The student ticket page automatically updates its status every eight seconds. The system also uses the Laravel scheduler to check expired calls every minute. In addition, queue activities can trigger these checks. For the scheduled checks to work properly, the Laravel scheduler must be running during deployment.


Figure 8. Final system architecture of ACESQ.




3.3 Database Design
The application database is MySQL and contains four principal tables: windows, staff_users, queue_records, and missed_calls. A window can have many staff accounts and queue records. A queue record can have many missed-call events. Constraints enforce a unique ticket number per window and queue date, a unique prefix per window, unique public ticket tokens, and a unique missed-call sequence per ticket. Student accounts are not created; the name is stored on the queue record, and a nullable 64-character hash supports active-ticket checks by browser session.

Section 3.4.

Figure 9. Final entity relationship diagram of ACESQ.




3.4 Data Dictionary
Table 3 describes the implemented database fields. Numeric fields are unsigned where specified. MySQL represents boolean values as TINYINT(1). Nullable fields support legacy records or optional relationships; new ticket issuance requires a student name and configured window prefix.

Table 3. Data dictionary of the implemented ACESQ database.

Table
Field
Data type
Key / nullability
Description
windows
window_id
BIGINT UNSIGNED
PK
Unique service-window identifier.
windows
window_name
VARCHAR(255)
Unique
Display name of the service window.
windows
queue_prefix
VARCHAR(8)
Unique; nullable
Prefix shown before ticket digits.
windows
is_active
TINYINT(1)
 
Whether students can join this window.
windows
created_at / updated_at
TIMESTAMP
Nullable
Creation and update timestamps.
staff_users
staff_id
BIGINT UNSIGNED
PK
Unique staff or administrator identifier.
staff_users
window_id
BIGINT UNSIGNED
FK; nullable
Assigned staff window; null for an administrator.
staff_users
full_name
VARCHAR(255)
 
Name displayed for the account.
staff_users
username
VARCHAR(255)
Unique
Login name; application validation allows up to 100 characters.
staff_users
password_hash
VARCHAR(255)
 
Hashed password; plain text is not stored.
staff_users
role
ENUM('staff','admin')
 
Role used for access control.
staff_users
created_at / updated_at
TIMESTAMP
Nullable
Creation and update timestamps.
queue_records
queue_id
BIGINT UNSIGNED
PK
Unique queue-record identifier.
queue_records
window_id
BIGINT UNSIGNED
FK
Window for the ticket; deletion restricted while tickets exist.
queue_records
student_name
VARCHAR(255)
Nullable for legacy records
Name entered and shown to staff.
queue_records
ticket_prefix
VARCHAR(8)
Nullable for legacy records
Prefix snapshot retained on the issued ticket.
queue_records
student_session_hash
VARCHAR(64)
Nullable; indexed
SHA-256 hash used for duplicate-ticket detection.
queue_records
queue_date
DATE
 
Date used for numbering and daily queue operations.
queue_records
queue_number
INT UNSIGNED
Unique with window_id and queue_date
Sequential number for a window and date.
queue_records
queue_position
INT UNSIGNED
 
Current waiting-list position.
queue_records
status
ENUM
 
waiting, called, present, completed, or forfeited.
queue_records
missed_count
TINYINT UNSIGNED
 
Number of missed calls, up to three.
queue_records
terms_agreed
TINYINT(1)
 
Whether the student accepted the terms.
queue_records
public_token
UUID / CHAR(36)
Unique
Token used in the student ticket URL.
queue_records
called_at
TIMESTAMP
Nullable
Time the current call began.
queue_records
created_at / updated_at
TIMESTAMP
Nullable
Creation and update timestamps.
missed_calls
missed_id
BIGINT UNSIGNED
PK
Unique missed-call event identifier.
missed_calls
queue_id
BIGINT UNSIGNED
FK
Associated ticket; events cascade on ticket deletion.
missed_calls
call_number
TINYINT UNSIGNED
Unique with queue_id
Missed-call sequence for the ticket.
missed_calls
slots_pushed
INT UNSIGNED
Nullable
Five, ten, or null for forfeiture.
missed_calls
missed_at
TIMESTAMP
 
Time the missed call was recorded.


3.5 System Modules
Authentication / User Accounts
The Authentication / User Accounts module manages access to the ACESQ system. It allows authorized staff and administrators to log in using their registered accounts. Each user is given access based on their assigned role. Staff members can only access the service window assigned to them, while administrators have access to system management features.
Dashboard
The Dashboard module serves as the main interface after users log in. It provides access to the system's features based on the user's role. Staff can view and manage their assigned queue, check waiting tickets, call the next student, and update ticket statuses. Administrators can access the management area to oversee service windows, user accounts, and queue activities.
Core Transaction Module
The Core Transaction module handles the main queueing activities of ACESQ. It allows students to select an active service window, enter their name, agree to the terms and conditions, and receive a daily queue ticket. Students can monitor their ticket status through a web browser without creating an account. Staff can manage the queue by calling the next student, marking tickets as present or completed, and handling missed calls. The system also applies automatic pushback rules to students who miss their calls.
Records Management
The Records Management module organizes and maintains the information stored in ACESQ. This includes student queue tickets, ticket numbers, service window assignments, queue statuses, staff accounts, administrator accounts, and queue activity records. It allows authorized users to view and manage the records needed for daily queue operations.
Reports
The Reports module provides access to queue-related information and activity records. Administrators can review queue activities, ticket statuses, and service window transactions. These records help monitor the daily queueing process and provide information about how the system is being used.
Administration / Settings
The Administration / Settings module provides management functions for authorized administrators. It allows them to create and manage service windows, manage staff and administrator accounts, and oversee queue activities. Access to these administrative features is restricted to authorized users to help maintain the security and proper operation of ACESQ.













3.6 User Roles and Permissions

Role
Permissions and access
Student (no account)
Select an active window; enter a name; accept terms; receive or reopen the active ticket; view ticket status, position, estimated wait, now-serving ticket, and countdown. Cannot access staff or administrator pages.
Service-window staff
Sign in; view only the assigned window; call the next ticket; mark the current student present; complete a transaction; review that window's waiting list and missed-call history.
Administrator
Manage windows and unique prefixes; create and manage staff/admin accounts; review the dashboard, queues, missed calls, and date-filtered analytics.
Automated application process
Expire calls after the five-minute grace period; record missed-call events; apply pushback or forfeiture; maintain daily queue positions.


Table 4 shows the roles in ACESQ and the actions available to each.











3.7 System Workflow
Figure 7 shows the overall process of ACESQ, from the moment a student opens the system to the completion of the transaction. The main steps are described below.

Figure 10. System workflow of ACESQ.
The student opens ACESQ, selects an active service window, enters a name, and reads the terms and conditions. A queue number is issued only after the student agrees.
The system checks whether the student already has an active ticket under the same name or browser session. If so, ACESQ opens the existing ticket. If not, it issues the next daily number for the selected window, with the window’s prefix.
The student monitors the live queue status from a personal device. The ticket page refreshes every eight seconds and shows the queue position, the ticket being served, and the estimated wait.
The staff member calls the next waiting ticket, and the five-minute grace period countdown starts and is shown to the student.
If the student is present, the staff member marks the student as present and then marks the transaction as completed.
If the student is not present when the grace period ends, the system records a missed call. The ticket is pushed back five places on the first missed call and ten places on the second, and it returns to the waiting list.
On the third missed call, the ticket is forfeited and the student must get a new queue number.
Completed and forfeited tickets, and all missed calls, appear in the administrator’s queue, missed-call, and history pages.
3.8 Implemented User Interfaces

Figure 11. Dashboard of ACESQ.
Figure 11 shows the first screen a student sees. It lists the active service windows with their ticket prefixes. After choosing a window, the student enters a name, reads the queue terms, and agrees before a ticket is issued. If the student already has an active ticket, ACESQ opens that ticket instead of issuing another. 
Login Page

Figure 12. Login page of ACESQ.

Figure 12 shows the login page used by service-window staff and administrators. Users enter their registered username and password to access the system. Passwords are stored in hashed form, and each account has a role that decides what the user can open after signing in. Staff can only manage the service window assigned to them, while administrators can open the management pages for windows, staff accounts, queues, missed calls, and queue history. Students do not use this page because they get a ticket without an account. 





Dashboard

Figure 12. Staff Dashboard of ACESQ.
Figure 12 shows the staff dashboard for the Registrar service window, opened after a staff member signs in. The header shows the signed-in account and its window, with a Sign out button. The Staff Queue Control banner names the window and holds the Call next student button, which calls the next waiting ticket. Three summary cards show how many students are waiting in line, the ticket now being served, and the number of missed calls today. Below them, the Waiting List table shows each ticket’s queue position, ticket number, student name, missed calls out of three, and pushback history. Ticket numbers stay fixed while positions change when a ticket is pushed back. Staff see only the queue of their own assigned window. 


Figure 13. Admin Dashboard of ACESQ.

Figure 13 shows the main dashboard of the ACESQ system for administrators. It displays the number of open service windows, staff accounts, tickets issued today, and students waiting or being served. It also provides a recent activity table showing ticket numbers, student names, assigned windows, ticket statuses, and issue times. The sidebar allows administrators to navigate to service windows, staff accounts, today's queues, queue history and analytics, and missed calls. 







Main Transaction Page

Figure 14. 

Figure 15.
Figure 14 and 15 shows the student queue registration and live ticket monitoring features of ACESQ. Students can review and accept the queue terms and conditions to receive a ticket. Once issued, they can view their ticket number, current queue status, position, number of students waiting, and the ticket currently being served. 


Figure 16
Figure 16 allows staff to manage their assigned service window by viewing the waiting queue, calling the next student, marking students as present, and updating ticket statuses after completing their transactions.










Records Page


Figure 17

Figure 18
Figure 17 and 18 allows administrators to monitor all student queue tickets across the service windows. It displays the service window, student name, ticket number, queue position, ticket status, and number of missed calls. It helps administrators track daily queue activities and check the progress of student transactions. 

Reports Page

Figure 19
Figure 19 allows administrators to review queue records and analyze daily service activities. It provides filters for selecting a queue date and service window, displays the total number of tickets and their statuses, and presents a chart showing the number of tickets per window. It also identifies the busiest service window, helping administrators monitor and evaluate queue activities.

Administrator Page

Figure 20
Figure 20 serves as the main interface for administrators to monitor the overall queue activities of ACES Tagum College. It displays the number of open service windows, staff accounts, tickets issued today, and students waiting or being served. It also shows recent ticket activities, including ticket numbers, student names, assigned service windows, statuses, and issue times. The sidebar provides access to the different administrative functions of the system. 

CHAPTER 4 – TESTING AND RESULTS
4.1 Functional Testing Results
Table 5 lists the major test cases performed on ACESQ together with the expected result of each. The actual result and status of each test were recorded by the tester.
Table 5. Functional test cases and results.
Test Case
Expected Result
Actual Result
Status
Remarks
Active-window selection
Only active windows are available; inactive windows cannot issue tickets.
The system displays only active service windows and prevents ticket issuance from inactive windows. 
Passed
Worked as expected. 
Required name and terms
A ticket is not issued when the required name or terms agreement is missing.
The system requires a student name and agreement to the terms before issuing a ticket. 
Passed
Required fields were properly validated. 
Per-window numbering
Ticket sequences are independent for each service window.
Each service window generates its own sequential ticket numbers. 
Passed
Ticket numbering worked correctly. 
Window prefixes
Tickets show a unique window prefix and retain the issued prefix if it later changes.
The system displays the correct prefix and keeps the original prefix on previously issued tickets. 
Passed
Window prefixes worked as expected. 
Duplicate active-ticket prevention
A student with an active ticket is directed to it rather than receiving another.
The system redirects students to their existing active ticket and prevents duplicate issuance. 
Passed
New ticket issuance worked correctly. 
Reissue after completion
A new ticket may be obtained after the previous ticket is completed.
The system allows students to obtain another ticket after their previous ticket is completed. 
Passed
[Remarks]
Live ticket status
The status endpoint returns ticket number, window, status, and queue-position information.
The student page displays the ticket information and automatically updates the queue status every eight seconds. 
Passed
Live ticket monitoring worked as expected. 
Missed-call escalation
The first miss pushes back five places, the second ten places, and the third forfeits the ticket.
The system correctly applies the five-place and ten-place pushback rules and forfeits the ticket after the third missed call. 
Passed
Missed-call rules worked correctly. 
Staff scope and roles
Staff actions are limited to the assigned window; staff cannot open administrator pages.
The system restricts staff access to their assigned service window and prevents unauthorized administrator access. 
Passed
Role restrictions worked as expected. 
Administrator pages and analytics
Pages are separate; date/window filtering, volume counts, and invalid-filter validation work.
The administrator pages display queue records, apply date and window filters, show ticket counts, and validate filter inputs. 
Passed
Administrative functions worked correctly. 
Staff and window management
Administrator account creation hashes passwords and window prefixes are unique.
The system successfully creates staff accounts, securely hashes passwords, and maintains unique service window prefixes. 
Passed
Account and window management worked as expected. 


4.2 Integration Testing
Integration testing checked that the connected modules of ACESQ work correctly together. The scenarios in Table 6 were run from start to finish using sample windows, staff accounts, and queue records.
Table 6. Integration test scenarios and results.
Scenario
Modules Involved
Expected Outcome
Result
Student gets a number and staff sees it
Window selection, terms, ticket issuance, staff dashboard 
The new ticket appears in the waiting list of the correct window with the correct prefix. 
Passed
Duplicate active ticket 
Ticket issuance, duplicate-ticket check 
The existing ticket opens and no second ticket is created. 
Passed
Staff calls a ticket and the student sees it 
Staff dashboard, live ticket status, grace period 
The status changes to called, the ticket is shown as being served, and the countdown appears (within 8 seconds). 
Passed
Student is present and served 
Staff dashboard, live ticket status 
The ticket becomes present, then completed, and leaves the waiting list. 
Passed
Full missed-call cycle 
Grace period, missed-call handling, staff dashboard, live ticket status 
1st miss: pushed back 5 places. 2nd miss: pushed back 10 places. 3rd miss: forfeited. The waiting list, missed-call history, and student page all show each change. 
Passed
Administrator reviews activity 
Staff dashboard, administrator pages 
The tickets, missed-call events, and per-window counts match what was done in the earlier scenarios. 
Passed

 


4.3 User Acceptance Testing
User acceptance testing was conducted to find out whether ACESQ meets the needs of its intended users. 5 respondents took part: 5 students. Each respondent used ACESQ to perform the main tasks (getting a queue number, viewing the queue status, and, for staff, calling and updating numbers) and then answered the questionnaire in Appendix C. The questionnaire used a five-point scale, where 5 means Strongly Agree and 1 means Strongly Disagree.
Table 7. Summary of user acceptance testing results.
Criteria
Mean Rating
Interpretation
Ease of use
[Mean]
[Interpretation]
Clarity of the queue status and countdown
[Mean]
[Interpretation]
Fairness and clarity of the no-show (pushback) rules
[Mean]
[Interpretation]
Speed of getting a queue number
[Mean]
[Interpretation]
Usefulness of the staff dashboard
[Mean]
[Interpretation]

 
[Summarize the comments and suggestions of the respondents here.]









4.4 Issues Encountered and Solutions Applied
Table 8. Issues encountered and solutions applied.
Issue / Problem
Cause
Solution Applied
The handling of absent students was unclear and could be unfair.
The first design forfeited the number of any student who was absent when called, and a later design sent the number to the end of the queue. Both were too harsh for a short, reasonable delay.
The group adopted the escalating pushback penalty: five slots on the first missed call, ten on the second, and forfeiture on the third. It is applied automatically and stated in the terms and conditions.
The queue status on the student's screen could become out of date.
ACESQ does not send push notifications, SMS, or email, so students rely on the status page in their browser.
The status page checks the server periodically, about every five to ten seconds, and shows the grace period countdown once the number is called.
A student could take several active tickets. 
Students do not have accounts, so the system could not tell whether the same student already had a ticket. 
The system checks the normalized name and a hashed browser-session identifier. If an active ticket exists, ACESQ opens it instead of issuing another. 
Expired calls were not applied unless someone acted on the queue. 
The grace period ends with the passage of time, not with a user action. 
The Laravel scheduler checks expired calls every minute, and queue requests also trigger the check. The scheduler must be running during deployment. 
Staff could reach queue actions outside their own window. 
Staff and administrator accounts share one table and need different access. 
Each account has a role and an assigned window. Staff actions are restricted to their window, and administrators use a separate area. 
Ticket numbers could change if a window’s prefix was edited later. 
Prefixes are editable by administrators, and tickets showed the window’s current prefix. 
The prefix is saved on each queue record when the ticket is issued, so earlier tickets keep their original number. 

4.5 Summary of Results
Table 9 relates each specific objective of the project (Section 1.3) to the feature of ACESQ that addresses it. All six objectives are covered by the implemented modules described in Chapter 3.
Table 9. Specific objectives and the features that address them.
Specific Objective
Implemented Feature
Section
1. Allow students to select an active service window without logging in. 
Window Selection module
3.5, 3.8
2. Require students to agree to the terms and conditions before receiving a ticket. 
Terms and Conditions Agreement 
3.5, 3.8
3. Generate sequential queue numbers independently for each service window. 
Queue Number Generation module
3.5
4. Provide live queue status, position, estimated waiting time, and grace period countdown. 
Live Queue Status page
3.5, 3.8
5. Automatically apply the escalating missed-call policy. 
Missed-Call Management Module 
3.5, 3.7
6. Provide staff with queue management tools for their assigned service windows. 
Staff Dashboard and Staff Authentication Modules 
3.5, 3.6, 3.8



CHAPTER 5 – CONCLUSION AND RECOMMENDATIONS
5.1 Conclusion
This project developed ACESQ: A Simple Campus Office Queuing System, a web-based application for the registrar windows of ACES Tagum College. The system addresses the long lines, crowded waiting areas, and unclear handling of absent students that occur during enrollment and other peak periods. Students select an active service window, enter a name, accept the queue terms, and receive a ticket without creating an account. They can then monitor the ticket from their own device instead of waiting in a physical line. Staff use a dashboard limited to their assigned window to call the next ticket, mark a student present, and complete a transaction. Administrators use separate pages to manage windows and staff accounts and to review current queues, missed calls, and date-filtered queue history.
The system also handles absent students automatically. A five-minute grace period starts when a ticket is called. If the student is not marked present in time, the first missed call moves the ticket back five places and the second moves it back ten, and the ticket returns to the waiting list. It is forfeited only on the third missed call. A briefly delayed student therefore keeps a place in the queue, and staff no longer have to decide manually how to treat absent students.
All six specific objectives of the project were achieved by the implemented system. Tickets are numbered sequentially for each window and day, with a distinct prefix. A student with an active ticket is directed to it instead of receiving another. The ticket page shows the status, position, ticket being served, estimated wait, and call countdown, and it refreshes every eight seconds. The system has limitations. It does not send push, SMS, or email notifications, it supports a single campus, and it needs the Laravel scheduler running during deployment. Even so, ACESQ shows that a simple queue system with automatic missed-call handling can make registrar queuing more organized and convenient for both students and staff.


5.2 Recommendations
The following improvements are recommended for future versions of ACESQ. They were outside the scope of the current project.
Notifications. Add SMS, email, or web push alerts that tell students when their number is nearing, so they no longer need to keep checking the status page.
Configurable queue rules. Let administrators change the grace period length and the pushback values from a settings page instead of using fixed values.
More detailed reports. Build on the current date-filtered analytics with average waiting time, average service time, and missed-call trends per window, to help plan staffing during enrollment.
Automated backups and monitoring. Add scheduled database backups and system monitoring, which are currently left to the deployment setup.
Queue display and QR access. Provide a QR code at the registrar area so students can open the queue page quickly, together with a display screen showing the tickets currently being served.
On-site option. Provide a staff-assisted kiosk or counter for students who do not have a device or internet access.
Wider coverage and priority handling. Extend the system to other campus offices, such as the cashier or the clinic, and to multiple campuses. Consider priority handling for senior citizens, persons with disabilities, and pregnant students.

5.3 References
[1] M. Abello, “Enrolment queuing system for a local college in Angeles City, Pampanga,” PUNLA, vol. 2020, pp. 209–215, 2020.
[2] G. S. Espinosa, M. R. Serrano, and H. A. Balmes, “Automated enrollment queueing system in a university towards student experience and operational efficiency,” MSEUF Research Studies, vol. 21, no. 1, 2024. [Online]. Available: https://ejournals.ph/article.php?id=24562
[3] M. L. Z. Mallari, J. S. Guintu, Y. C. Magalong, and D. S. Yap, “CLIQUE: A web-based queue management system with real-time queue tracking and notification of units for Angeles University Foundation Office of the University Registrar,” in Proc. Int. Conf. Industrial Engineering and Operations Management, Istanbul, Turkey, Mar. 7–10, 2022. [Online]. Available: https://ieomsociety.org/proceedings/2022istanbul/359.pdf


APPENDICES
APPENDIX A – USER MANUAL
This manual explains how students, service-window staff, and administrators use ACESQ. The screens are shown in Chapter 3 (Figures 11 to 21).
A.1 For Students (no account required)
Step 1. Open the ACESQ web address in a browser on a phone, tablet, or computer. The first screen lists the active service windows with their ticket prefixes (Figure 11).
Step 2. Choose the service window that matches the transaction. Inactive windows cannot issue tickets.
Step 3. Enter your name exactly as the staff should call it, then read the queue terms, including the five-minute grace period and the pushback policy.
Step 4. Tick the agreement box and submit. A ticket is issued only after the terms are accepted (Figure 15).
Step 5. The ticket page shows your ticket number, window, status, queue position, the ticket now being served, and an estimated wait (Figure 16). The page refreshes by itself every eight seconds, so it can stay open. Keep the page link so the ticket can be reopened.
Step 6. When your ticket is called, a five-minute countdown appears. Go to the window and tell the staff member your ticket number before the countdown ends.
Step 7. If you are not marked present in time, the ticket is pushed back five places on the first miss and ten places on the second. A third miss forfeits the ticket and a new one must be obtained.
If you open ACESQ again while you still have a waiting, called, or present ticket, the system shows that ticket instead of issuing a new one. A new ticket can be obtained after the previous one is completed or forfeited.
A.2 For Service-Window Staff
Step 1. Open the login page and enter the assigned username and password (Figure 12).
Step 2. On the staff dashboard, check the three summary cards: students waiting, ticket now being served, and missed calls today. The Waiting List shows each ticket’s position, number, student name, missed calls, and pushback history (Figures 13 and 17).
Step 3. Select Call next student to call the next waiting ticket. The five-minute grace period starts and the student sees the countdown.
Step 4. When the student arrives, mark the student as present.
Step 5. After the transaction is finished, mark it as completed so the ticket leaves the queue.
Step 6. If the student does not arrive, no action is needed. The system records the missed call and applies the pushback or forfeiture automatically.
Step 7. Select Sign out when finished. Staff can only see and manage their own assigned window.
A.3 For Administrators
Step 1. Log in with an administrator account. The dashboard shows open windows, staff accounts, tickets issued today, students waiting or being served, and recent ticket activity (Figures 14 and 21).
Step 2. Use Service Windows to add, edit, activate, or deactivate a window. Each window needs a unique ticket prefix.
Step 3. Use Staff Accounts to create staff or administrator accounts and to assign each staff account to a window. Passwords are stored in hashed form.
Step 4. Use Today’s Queues and Missed Calls to monitor current tickets, queue positions, statuses, and missed-call events (Figures 18 and 19).
Step 5. Use Queue History and Analytics to filter records by date and optional window, view the status totals, and compare ticket volume per window (Figure 20).

APPENDIX B – INSTALLATION / DEPLOYMENT GUIDE
ACESQ is a Laravel-based web application deployed using Wasmer. The system source code is maintained in GitHub and connected to Wasmer for deployment. The application uses a Wasmer-managed MySQL database and is accessed through its assigned public URL. This appendix documents the deployment setup, configuration, and verification process used to make ACESQ available online.
B.1 Minimum Requirements
Hosting Platform: Wasmer hosting account with an application deployment environment.
Source Code Repository: GitHub repository containing the ACESQ Laravel project.
PHP Runtime: PHP 8.4 configured in the deployment environment, compatible with the project's Laravel dependencies.
Database: Wasmer-managed MySQL database for storing system records, including service windows, staff accounts, queue records, and missed-call records.
Deployment Tools: GitHub for source code management and Wasmer for application hosting and deployment.
Application Configuration: Required environment variables for Laravel configuration, application key, database connection, and application URL.
Client Requirements: A computer, tablet, or smartphone with an updated web browser and an internet connection.
B.2 Deployment Steps
Step 1. Prepare the ACESQ Project Repository
The Laravel project was prepared locally and uploaded to GitHub. The repository used for deployment is:
GitHub Repository: https://github.com/jirahmaerafael-dev/ACESQ
The project files were committed and pushed to the main branch to make the source code available for deployment.
Step 2. Connect GitHub to Wasmer
The GitHub repository was connected to Wasmer to allow the hosting platform to access the ACESQ source code. The repository and main branch were selected as the source for the application deployment.
Step 3. Configure the Deployment Environment
The Wasmer application was configured to use PHP 8.4. The Laravel application's start command was set to:
php -S 0.0.0.0:8080 -t public
This command starts the PHP built-in web server, listens on port 8080, and serves the Laravel application through its public directory.
Step 4. Configure Environment Variables
The required Laravel environment variables were configured through the Wasmer application settings. These include the application environment, application key, application URL, and database connection settings.
The database configuration includes the database host, port, database name, username, and password required to connect to the managed MySQL database.
Step 5. Set Up the Managed MySQL Database
A MySQL database was configured through Wasmer's managed database service. The database connection details were used as environment variables in the application to allow ACESQ to access its database.
The database used for the online deployment is separate from the local database used during development.
Step 6. Deploy the Application
The application was deployed through Wasmer using the connected GitHub repository. The deployment process prepared the application environment and launched the Laravel application using the configured start command.
During deployment, configuration and runtime issues were checked through the deployment logs to ensure that the application could start properly.
Step 7. Apply Database Migrations
The Laravel migrations were prepared to create the database tables required by ACESQ. Since the application runs in a production environment, migrations must be executed using the production-safe command:
php artisan migrate --force
This allows the migrations to proceed without the interactive confirmation required in production.
Step 8. Access the Deployed Application
After a successful deployment, ACESQ can be accessed through its assigned public URL:
https://acesq.wasmer.dev
The URL can be opened using a web browser with an internet connection, allowing users to access the deployed system without running the application on their local computers.
Step 9. Verify the Deployment
The deployed application should be checked to ensure that the login page loads properly, database connectivity is working, and the system's main features are accessible. The queueing process, student queue status, staff operations, and queue management functions should be tested to confirm that the online deployment is functioning correctly.
B.3 Scheduler and Maintenance
ACESQ uses Laravel's scheduling mechanism to check expired calls and execute scheduled tasks. The scheduler is intended to run every minute to ensure that time-based queue processes are updated accordingly.
Wasmer provides a Jobs feature that can be used for background or scheduled task configuration. The scheduler must be configured to execute the Laravel schedule command at the required interval for the scheduled queue processes to work properly.
Application maintenance includes monitoring deployment logs, checking the database connection, and redeploying updates when changes are pushed to the connected GitHub repository. Changes committed and pushed to the main branch can be used as the source for subsequent deployments.
Database backups, data retention, and monitoring should be handled through the hosting and database services or arranged separately. After maintenance or redeployment, the application should be verified to ensure that its main functions remain accessible and operational.


APPENDIX C – TESTING FORMS / UAT FORMS
The forms below were used to evaluate ACESQ. Section 4.1 and Section 4.2 contain the functional and integration test sheets (Tables 5 and 6).
C.1 User Acceptance Testing Questionnaire
Instructions: After using ACESQ, rate each statement from 5 (Strongly Agree) to 1 (Strongly Disagree) by marking one box.
Respondent name (optional): ______________________   Role: ☐ Student   ☐ Staff   Date: ____________
No.


Statement
5
4
3
2
1
1
ACESQ is easy to use.
☐
☐
☐
☐
☐
2
The queue status and countdown are clear.
☐
☐
☐
☐
☐
3
The no-show (pushback) rules are fair and clear.
☐
☐
☐
☐
☐
4
Getting a queue number is quick.
☐
☐
☐
☐
☐
5
The staff dashboard is useful for managing the queue.
☐
☐
☐
☐
☐

Comments and suggestions: ________________________
_______________________________________________
C.2 User Acceptance Testing Tally Sheet
Record each respondent’s rating, then compute the mean of each criterion for Table 7.
No.
Ease of use
Status and countdown
Pushback rules
Speed
Staff dashboard
1
 
 
 
 
 
2
 
 
 
 
 
3
 
 
 
 
 
4
 
 
 
 
 
5
 
 
 
 
 
Mean
 
 
 
 
 

C.3 Test Case Sheet
Test Case
Expected Result
Actual Result
Status (Passed / Failed)
Tester
 
 
 
 
 
 
 
 
 
 












APPENDIX D – GITHUB REPOSITORY AND GROUP CONTRIBUTION
Repository: jirahmaerafael-dev/ACESQ 
Table 11. Group members and contributions.
Member
Role
Contributions
Rafael, Jirah Mae O
Group Leader
[Main tasks and modules]
 Benjamin, John Mark
Member
[Main tasks and modules]
 Gingoyon, Jeds Kyle G.
Member
[Main tasks and modules]
 Cabaral, Norhia N.
Member
 


APPENDIX E – OTHER SUPPORTING DOCUMENTS
The following supporting materials accompany this documentation:
1. System diagrams: architecture (Figures 1 and 8), use case diagram (Figure 2), and entity relationship diagram (Figures 3 and 9).
2. Screenshots of the implemented student, staff, and administrator interfaces (Figures 4 to 7 and Figures 11 to 21).
3. Project timeline (Section 2.11) and the data dictionary (Table 3).
4. Testing records: functional test cases (Table 5), integration scenarios (Table 6), and the UAT questionnaire and tally sheet (Appendix C).
5. Source code and commit history: the GitHub repository listed in Appendix D. 

FINAL DOCUMENTATION CHECKLIST
☐ Chapter 1 has been updated from proposal language to final-project language.
☐ Chapter 2 reflects the methodology and tools actually used.
☐ Chapter 3 contains the completed system design, modules, workflow, and actual screenshots.
☐ Chapter 4 contains actual testing results, not only testing plans.
☐ Chapter 5 contains a conclusion based on the completed project and realistic recommendations.
☐ The final ERD, use case diagram, architecture, and implemented modules are consistent with one another.
☐ All screenshots are readable and have captions or explanations.
☐ The data dictionary matches the actual database.
☐ GitHub repository information and member participation evidence are included.
☐ Grammar, spelling, page numbering, tables, and figure labels have been reviewed.
☐ All group members have reviewed the final documentation before submission.

Instructor Evaluation / Notes
Project Title: _______________________________________________________
Group: _______________________________________________________
Status:  ☐ Accepted   ☐ Accepted with Revision   ☐ For Revision _______________________________________________________
Comments: _______________________________________________________
