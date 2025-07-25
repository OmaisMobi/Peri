---
title: Biometric Working
---

This document details the operational aspects of the Biometric Management System, from submitting a request to its approval and how it's displayed.

## 1. Submitting a Biometric Request

Employees can submit biometric requests through a dedicated form to adjust their attendance records.

### Key Fields:

-   **Period**: Specifies the time of the day for which the biometric adjustment is requested. Options include `Morning`, `Break Start`, `Break End`, and `Evening`.
-   **Date & Time**: The exact date and time for the biometric adjustment. This is a required field.
-   **Reason**: A mandatory field to explain why the biometric adjustment is needed. Users can select from predefined reasons (e.g., 'Forgot to check in', 'Forgot to check out', 'Technical issue', 'Official business') or type their own custom reason.
-   **Status**: This field is primarily for approvers. By default, new requests are `pending`. If a user with `Approve` permission or an `Admin` creates a request, it can default to `approved`.
-   **Rejection Reason**: A mandatory text area that becomes visible and required only when the `status` is set to `rejected`.

### Form Behavior:

-   **Disabled Fields**: Once a biometric request's `status` is `approved` or `rejected`, all input fields become disabled to prevent further modifications.

## 2. Biometric Request Approval Workflow

Biometric requests typically go through an approval process.

### Approval Steps:

1.  **Initial Status**: When an employee submits a request, its initial `status` is `pending`.
2.  **Approver Actions**: Users with the `Approve` permission or `Admin` role can view and modify the `status` of a biometric request.
    -   They can change the status to `approved` or `rejected`.
    -   If `rejected`, they must provide a `rejection reason`.
3.  **Visibility of Status Field**: The `status` dropdown is hidden from the user who created the request and from users who do not have the necessary approval permissions.

## 3. Viewing Biometric Requests

Biometric requests are displayed in a table format, providing an overview of all requests.

### Table Functionality:

-   **Default Sort**: The table is sorted by `timedate` in descending order by default (newest requests first).
-   **Search**: Users can search for requests by `Employee Name`.
-   **Actions**:
    -   **Edit**: Visible to `Admin` or users with `Create Request` permission. Editing is disabled if the request is already `approved` or `rejected`.
    -   **Delete**: Visible to `Admin` or users with `Create Request` permission. Deletion is disabled if the request is `approved`.

## 4. Permissions and Access Control

The system implements granular permissions to control access to biometric request functionalities:

-   **`View`**: Determines if a user can view any biometric requests. Requires `Admin` role or `View` permission.
-   **`Create`**: Determines if a user can create a biometric request. Requires `Admin` role or `Create Request` permission, and the user must have attendance enabled.
-   **`Approve`**: Determines if a user can approve biometric requests. Requires `Admin` role or `Approve`.
