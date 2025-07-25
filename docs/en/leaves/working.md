---
title: Working With Leaves
group: Leave Requests
---

This document details the operational aspects of the Leave Management System, from submitting a request to its approval and how leave balances are managed.

## 1. Submitting a Leave Request

Employees can submit leave requests through a dedicated form. The form dynamically adjusts based on the selected leave type.

### Leave Types:

-   **Regular Leave**: For full-day absences. Requires a `Starting Date` and `Ending Date`. The system calculates the duration in days, excluding holidays.
-   **Half Day Leave**: For half-day absences. Requires a `Date` and a `Half Day Timing` (First Time or Second Time).
-   **Short Leave**: For short absences during a workday. Requires a `Date`, `Starting Time`, and `Ending Time`. The system calculates the duration in minutes.

### Key Fields:

-   **Duration**: Selects the type of leave (Regular, Half Day, Short Leave). Available options may vary based on the user's attendance type (e.g., 'offsite' or 'hybrid' users only see 'Regular Leave').
-   **Type**: Specifies the specific leave category (e.g., Annual Leave, Sick Leave). Options are filtered based on the user's gender and marital status, as configured in the system's leave types.
-   **Payment**: Indicates whether the leave is paid or unpaid. This field is automatically disabled if the user has exhausted their allowed paid leaves for the selected leave type within the current period (e.g., month, quarter, year).
    -   **Hint**: A helpful hint displays the number of paid leaves used versus allowed for the selected leave type and period.
-   **Reason**: A mandatory text area for the employee to provide details about their leave request.
-   **Document**: An optional file upload field for attaching supporting documents (e.g., medical certificates). Supported formats include PDF, JPEG, and PNG, with a maximum size of 5MB.

### Leave Duration Calculation (Regular Leave):

For regular leaves, the system calculates the effective number of leave days by subtracting company holidays that fall within the requested leave period. This ensures that employees are not debited for days they would not have worked anyway.

## 2. Leave Approval Workflow

Leave requests follow a multi-level approval hierarchy. The `Approval` dropdown (visible to authorized approvers) allows them to take action.

### Approval Steps:

1.  **Hierarchy Display**: The form displays an `Approval Wizard` showing the defined approval steps (levels) for the user who submitted the leave. This includes the roles involved at each level.
2.  **Current Level**: The system identifies the current approval level based on the latest action taken on the leave request.
3.  **Role-Based Actions**: The options in the `Approval` dropdown depend on the current user's role and their assigned permission (`recommend` or `approve`) at the current level in the approval hierarchy:
    -   **Recommend**: If the user has 'recommend' permission, they can `Forward Leave` (to the next level) or `Reject Leave`.
    -   **Approve**: If the user has 'approve' permission, they can `Approve Leave` or `Reject Leave`.
4.  **Locked Fields**: The leave request form fields are locked (`disabled`) under the following conditions:
    -   The leave has reached a final status.
    -   The creator of the leave can only edit it if its status is `Pending`. Once it's `Forwarded` or `Pending Cancellation`, the creator cannot edit it.
    -   If the current user is not an authorized approver/recommender for the current step in the approval hierarchy.

### Cancellation Requests:

-   **Initiating Cancellation**: An employee can request to `Cancel` an `Approved` leave. This action requires providing a `Cancellation Reason`.
-   **Cancellation Approval**: Once a cancellation is requested, the leave status changes to `Pending Cancellation`. A separate approval process is initiated, where designated approvers can `Approve Cancellation` or `Reject Cancellation`.
-   **Cancellation Details**: The form displays a `Cancellation Details` section for leaves with cancellation-related statuses, showing the reason and request timestamp.

## 3. Leave Balances and History

The system provides employees and authorized personnel with clear visibility into leave balances and past leave history.

### Leave Balance Table:

-   The form includes a `Leave Balance Table` that displays the total allowed, used, and remaining leaves for each eligible leave type for the user.
-   Balances are calculated based on the configured duration for each leave type (e.g., monthly, quarterly, annually) and only count `paid` and `approved` leaves.

### Last Approved Leaves:

-   A `Last Approved Leaves` section shows the three most recently approved leave requests for the user, including their type, duration, reason, and approval date. This provides a quick overview of recent leave activity.

## 4. Leave Logs and Changelog

Every action taken on a leave request is logged, providing a comprehensive audit trail.

-   **Approval Logs**: The `status` column in the leave table displays the latest status and the role that set it (e.g., "HR Manager: Approved").
-   **Changelog**: The form includes a `Changelog` section that specifically tracks modifications made by editors, showing remarks like "Updated by [Role Name]". This helps in understanding the progression of a leave request through the approval process.
