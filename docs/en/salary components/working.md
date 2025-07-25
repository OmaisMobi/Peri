---
title: Managing Components
description: How to create, view, edit, and delete salary components.
---

This section details how to manage salary components within the system.

## Creating a New Salary Component

To create a new salary component, navigate to the Salary Components section and click on the "+ Add" button. You will be presented with a form to define the component's properties:

-   **Category:** Select an existing category for the salary component, or create a new one if needed. Categories help organize different types of earnings and deductions.
-   **Component Type:** Choose whether the component is an `Earning` (adds to salary) or a `Deduction` (subtracts from salary).
-   **Title:** Provide a unique internal name for the salary component.
-   **Title on Payslip:** Enter the name that will be displayed on the employee's payslip for this component.
-   **Calculation Type:** Select how the component's value is determined:
    -   **Fixed Amount:** A specific numerical value.
    -   **Percentage of Base Salary:** A percentage of the employee's base salary.
-   **Amount:** Enter the value for the component. The input field will automatically show a currency symbol or a percentage sign based on the selected `Calculation Type`.
-   **Mark as one-time deduction:** (Visible for `Deduction` type) Check this box if the deduction is a one-time occurrence.
-   **Mark as taxable:** (Visible for `Earning` type) Check this box if the earning component is subject to tax.

## Viewing and Filtering Salary Components

The salary components are displayed in a table with the following columns:

-   **Title:** The internal name of the component.
-   **Category:** The category to which the component belongs.
-   **Type:** Indicates whether it's an `Earning` or a `Deduction`.
-   **Amount:** The value of the component, displayed with the appropriate currency or percentage symbol.
-   **Active:** Indicates if the component is currently active.

You can search for components by their title using the search bar. Additionally, you can filter the table by:

-   **Component Type:** Filter by `Earning` or `Deduction`.
-   **Category:** Filter by specific salary component categories.
-   **Active Status:** Filter to show `Active Only` or `Inactive Only` components.

## Editing and Deleting Salary Components

-   **Editing:** To edit a salary component, click the "Edit" action next to the component in the table. You can modify its properties as needed.
-   **Deleting:** To delete a salary component, click the "Delete" action next to the component. A confirmation prompt will appear before deletion.

**Important Note:** Salary components cannot be modified or deleted if there is an unfinalized payroll (draft, pending approval, or rejected) in process. An alert message will be displayed if you attempt to perform these actions during an active payroll process.
