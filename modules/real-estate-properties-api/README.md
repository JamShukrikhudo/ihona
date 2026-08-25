# Real Estate Properties API

This package exposes only the authorized Properties module through the
versioned `/api/v1/real-estate/properties` contract. It validates transport
input and exposes bounded search, range, type, country, energy, score, and
featured filters. It delegates mutations to the core action; the core remains
the authority for persistence and invariants.
