# Real Estate Parties API

This package exposes only the authorized, team-scoped Parties contract at
/api/v1/real-estate/parties.

Consent is managed explicitly with `POST /{party}/consent` and a boolean
`granted` payload; it is never treated as an incidental profile update.
