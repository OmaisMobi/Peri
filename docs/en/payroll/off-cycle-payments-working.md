# How One-Time Payments Work

This document details the operational aspects of managing individual one-time payment records, from viewing their details to taking action on them.

## 1. Overview of One-Time Payment Records

Individual one-time payment records are displayed in a table format, providing a summary for each payment.

### Table Columns:

*   **Employee**: The name of the employee who received the payment.
*   **Period**: The period for which the payment was made.
*   **Earnings**: The total earnings for that payment, formatted with the appropriate currency symbol.
*   **Deductions**: The total deductions for that payment, formatted with the appropriate currency symbol.
*   **Net Pay**: The final net amount paid, formatted with the appropriate currency symbol.
*   **Status**: The current state of the payment record, indicated by a colored badge:
    *   `Pending Approval` (warning/yellow)
    *   `Rejected` (danger/red)
    *   `Approved` (success/green)
    *   If a payment is `Rejected`, the reason for rejection is displayed below the status.

### Sorting:

*   The table is sorted by the `Period Start` date in descending order by default (most recent payments first).

## 2. Actions on One-Time Payment Records

Various actions can be performed on individual one-time payment records, depending on their current status and your permissions.

### Available Actions:

*   **View**: This action allows you to see a detailed payslip for the specific payment. The visibility of this action depends on the payment's status and your role:
    *   For `Pending Approval` or `Rejected` payments, it's visible to Administrators and users with payroll creation or approval permissions.
    *   For `Approved` payments, it's visible to Administrators and users with payroll record management or view records permissions.
*   **Approve**: If a payment record is `Pending Approval`, authorized users can `Approve` it. This action requires confirmation and will update the status to `Approved`.
*   **Reject**: If a payment record is `Pending Approval`, authorized users can `Reject` it. This action requires providing a `Reason for Rejection` and will update the status to `Rejected`.
*   **Delete**: This action allows authorized users to delete a one-time payment record. It is visible for `Pending Approval` or `Rejected` payments and only to Administrators or users with payroll creation permissions.

## 3. Access Control

*   **Viewing One-Time Payment Records**: Access to view these records is determined by your role and permissions:
    *   Administrators, payroll approvers, and payroll record managers can always view all records.
    *   Employees (users with `payroll.viewRecords` permission) can only view their own `Approved` one-time payment records.

This system provides a clear and controlled way to manage individual one-time payments.