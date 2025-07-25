---
title: Configuring Policies
description: How to configure various attendance policies.
---

This section details how to configure each attendance policy within the system.

## Late Minutes Policy

This policy handles late arrivals and early departures.

-   **Enable:** Toggle this to activate or deactivate the Late Minutes Policy.
-   **Count Late Minutes:** If enabled, the system will count minutes an employee is late.
-   **Count Early Leave Minutes:** If enabled, the system will count minutes an employee leaves early.
-   **Allow Minutes Offset:** If enabled, employees can compensate for late minutes by staying late.

## Single Biometric Policy

This policy defines how the system handles instances where only one biometric punch is recorded for a day.

-   **Enable:** Toggle this to activate or deactivate the Single Biometric Policy.
-   **Behavior:** Choose how to mark the day if only one biometric punch is recorded:
    -   **Mark as Half Day:** The day will be considered a half-day.
    -   **Mark as Biometric Missing:** The day will be marked as having a missing biometric punch.

## Grace Minutes Policy

This policy grants employees grace minutes for arrival.

-   **Enable:** Toggle this to activate or deactivate the Grace Minutes Policy.
-   **Grant Minutes:** Specify the number of grace minutes allowed.
-   **For Number of Days:** Define the number of days for which the grace minutes are applicable.
-   **Duration:** Choose the duration for the grace period:
    -   **Per Day:** Grace minutes apply daily.
    -   **Per Month:** Grace minutes apply per month (maximum 31 days).

## Overtime Policy

This policy sets rules for calculating and limiting overtime.

-   **Enable:** Toggle this to activate or deactivate the Overtime Policy.
-   **Overtime Start Delay Minutes:** Set the delay in minutes after which overtime begins when a shift ends.
-   **Maximum Overtime Minutes:** Define the maximum total overtime minutes allowed.
-   **Duration:** Choose how often the overtime limit resets:
    -   **Per Day:** Overtime limit resets daily.
    -   **Per Month:** Overtime limit resets monthly.

## Sandwich Leave Policy

This policy manages how leaves are treated when taken around official off days.

-   **Enable:** Toggle this to activate or deactivate the Sandwich Leave Policy.
-   **Option:** Choose how the policy applies:
    -   **Apply only when leave is taken before an off day**
    -   **Apply only when leave is taken after an off day**
    -   **Apply when leave is taken both before and after an off day**
    -   **Apply when leave is taken either before or after an off day**
