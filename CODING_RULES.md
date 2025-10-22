# Coding Rules and Standards

This document outlines the coding standards and architectural principles that must be followed throughout the project.

## File Length and Structure

- **Never allow a file to exceed 500 lines.**
- **If a file approaches 400 lines, break it up immediately.**
- **Treat 1000 lines as unacceptable, even temporarily.**
- Use folders and naming conventions to keep small files logically grouped.

## Object-Oriented First (Koop First)

- Every functionality should be in a dedicated class, struct, or protocol, even if it's small.
- Favor composition over inheritance, but always use object-oriented thinking.
- Code must be built for reuse, not just to "make it work."

## Single Responsibility Principle

- Every file, class, and function should do one thing only.
- If it has multiple responsibilities, split it immediately.
- Each view, manager, or utility should be laser-focused on one concern.

## Modular Design

- Code should connect like Lego - interchangeable, testable, and isolated.
- Ask: "Can I reuse this class in a different green or project?" If not, refactor it.
- Reduce tight coupling between components. Favor dependency injection or protocols.

## Manager and Coordinator Patterns

- Use ViewModel, Manager, and Coordinator naming conventions for logic separation:
  - **UI logic** → ViewModel
  - **Business logic** → Manager
  - **Navigation/state flow** → Coordinator
- Never mix views and business logic directly.

## Function and Class Size

- Keep functions under 30-40 lines.
- If a class is over 200 lines, assess splitting into smaller helper classes.

## Naming and Readability

- All class, method, and variable names must be descriptive and intention-revealing.
- Avoid vague names like `data`, `info`, `helper`, or `temp`.

## Scalability Mindset

- Always code as if someone else will scale this.
- Include extension points (e.g., protocol conformance, dependency injection) from day one.

## Avoid God Classes

- Never let one file or class hold everything (e.g., massive ViewController, ViewModel, or Service).
- Split into UI, State, Handlers, Networking, etc.

---

## Enforcement

These rules are not suggestions - they are mandatory standards. Code reviews should enforce these principles, and any violations should be addressed immediately before merging.

## Examples

### Good Structure
```
UserManager/
├── UserManager.php          (business logic)
├── UserViewModel.php        (UI logic)
├── UserCoordinator.php      (navigation/flow)
└── UserValidator.php        (validation logic)
```

### Bad Structure
```
UserController.php           (1000+ lines doing everything)
```

### Good Function
```php
public function validateEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}
```

### Bad Function
```php
public function processUserData($data) // Vague name, unclear purpose
{
    // 200 lines of mixed responsibilities
}
```

