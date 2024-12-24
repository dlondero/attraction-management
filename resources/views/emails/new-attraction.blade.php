New Attraction Added
===================

A new attraction has been added to the system:

Name: {{ $attraction->name }}
Location: {{ $attraction->location }}
Price: ${{ number_format($attraction->price, 2) }}
Description: {{ $attraction->description }}

You can view this attraction at:
{{ route('attractions.show', $attraction) }}

---
© {{ date('Y') }} Attraction Management. All rights reserved.
