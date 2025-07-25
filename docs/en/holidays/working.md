---
title: Plan Your Holidays
---

This document details the operational aspects of the Holiday Management System, from creating a holiday to how it's displayed and managed.

## 1. Creating a Holiday

Administrators can create new holiday entries through a dedicated form.

### Key Fields:

-   **Type**: Categorizes the holiday. Options include:
    -   Weekend
    -   Religious Day
    -   National Day
    -   Unplanned
    -   Other
-   **Starting Date**: The first day of the holiday.
-   **Ending Date**: The last day of the holiday. This date must be on or after the Starting Date.
-   **Apply To**: Determines who the holiday applies to. Options are:
    -   **All**: The holiday applies to everyone in the organization.
    -   **Shift**: The holiday applies only to employees assigned to specific shifts. When selected, a field to choose `Shift(s)` appears.
    -   **Department**: The holiday applies only to employees within specific departments. When selected, a field to choose `Department(s)` appears.
    -   **Employee**: The holiday applies only to specific individual employees. When selected, a field to choose `Employee(s)` appears.
-   **Remarks**: An optional text area for adding any additional notes or descriptions about the holiday.

## 2. Viewing and Managing Holidays

All planned holidays are displayed in a table format, providing a clear overview.

### Table Columns:

-   **Starting Date**: The beginning date of the holiday period.
-   **Ending Date**: The end date of the holiday period.
-   **Type**: The category of the holiday (e.g., Weekend, National Day).
-   **Applies To**: Indicates whether the holiday applies to all, specific shifts, departments, or employees.
-   **Remarks**: Any additional notes provided for the holiday.

### Table Functionality:

-   **Filtering**: Holidays can be filtered by their `Type`.
-   **Sorting**: The table is sorted by `Starting Date` in descending order by default (most recent holidays first).
-   **Actions**:
    -   **Edit**: Allows authorized users to modify the details of an existing holiday.
    -   **Delete**: Allows authorized users to remove a holiday entry.

## 3. Permissions and Access Control

The system implements specific permissions to control who can manage holidays:

-   **Viewing Holidays**: Requires an `Admin` role or specific permission to view holidays.
-   **Managing Holidays**: Requires an `Admin` role or `Manage` permission to create new holidays, edit and delete them.

This ensures that only authorized personnel can manage the organization's holiday schedule.
