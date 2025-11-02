### Service Layer Refactor (Nov 2025)
- Introduced `SubscriptionService` to encapsulate all subscription-related logic.
- Controller now delegates to service methods:
  - `getUserSubscriptions()`
  - `getSubscription()`
  - `cancelSubscription()`
- Improved testability, maintainability, and reduced controller complexity.
- All business rules (ownership, admin overrides, cancellation logic) centralized in the service.


