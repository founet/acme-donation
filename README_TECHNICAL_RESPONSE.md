# 🏗 Architecture Choices

I implemented a **Clean Architecture** to enforce a strict separation of concerns. The project is organized into three main layers:

- **Domain**: Contains core entities (`Campaign`, `Donation`, `User`) and business rules, independent from Laravel.
- **Application**: Use cases like `CreateDonationUseCase`, `UpdateCampaignUseCase` orchestrate logic and define ports (interfaces).
- **Infrastructure/Interface**: Includes controllers, repositories, external services (e.g., Eloquent, Stripe), acting as adapters.

This structure ensures the business rules remain testable, reusable, and decoupled from delivery concerns like HTTP or database.

# 📐 Assumed Hypotheses

- The app assumes that users are authenticated via Laravel Sanctum.
- In production, I would have preferred using JWT with refresh tokens for secure session handling, token expiration, and logout support.
- Only authenticated users can create or modify campaigns or donate.
- Campaigns must have valid dates (`start ≥ today`, `end > start`) to be valid.
- To support this flexibility, **payment processing was abstracted** behind a `PaymentServiceInterface`, which can be adapted later to use Stripe, PayPal, or another provider.

# 📋 Business Rules Overview

- Campaigns cannot be created without valid dates.
- Donations are only allowed for active campaigns.
- Authorization is enforced based on the campaign creator or admin role.
- All donation attempts require a payment confirmation and are linked to a user and a campaign.

These rules are encapsulated in use cases, not controllers, ensuring consistency across all entry points.

# 🧱 Faced Constraints

- The main technical constraint was enforcing proper JSON responses for all API errors, especially for failed validations. Laravel’s default behavior was insufficient for a full API-first architecture.
- I also needed to ensure that domain objects could be returned cleanly via JSON without leaking infrastructure details.

# 🔧 Solutions Applied

- A custom `ApiResponse` helper was implemented to ensure consistent JSON format for all responses.
- I overrode `failedValidation()` in `FormRequest` classes to return structured validation errors in JSON.
- All Eloquent model instances are converted to domain entities, avoiding business logic pollution in infrastructure.
- External services (e.g., payments) are abstracted with interfaces, improving testability.
- Each use case is unit tested with Mockery and focuses solely on business behavior, not framework internals.
