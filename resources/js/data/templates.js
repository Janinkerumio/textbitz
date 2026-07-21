const templatesDefault = [
    {
      "id": 1,
      "category": "Welcome",
      "title": "New Subscriber Welcome",
      "message": "Hi {name}! 👋 Welcome to {business_name}. We're excited to have you on board. Reply STOP to opt out anytime.",
      "variables": ["name", "business_name"],
      "tags": ["welcome", "onboarding"],
      "icon": "hand-wave",
      "color": "#4F46E5"
    },
    {
      "id": 2,
      "category": "Welcome",
      "title": "First Purchase Welcome",
      "message": "Welcome to the {business_name} family, {name}! 🎉 Here's 10% off your next order with code WELCOME10.",
      "variables": ["name", "business_name"],
      "tags": ["welcome", "discount"],
      "icon": "gift",
      "color": "#4F46E5"
    },
    {
      "id": 3,
      "category": "Thank You",
      "title": "Thank You for Your Purchase",
      "message": "Thank you for shopping with {business_name}, {name}! Your order #{order_id} is being prepared. We appreciate you! 🙏",
      "variables": ["name", "business_name", "order_id"],
      "tags": ["thank you", "order"],
      "icon": "heart",
      "color": "#16A34A"
    },
    {
      "id": 4,
      "category": "Thank You",
      "title": "Thanks for Visiting",
      "message": "Thanks for visiting {business_name} today, {name}! We hope to see you again soon. 😊",
      "variables": ["name", "business_name"],
      "tags": ["thank you", "visit"],
      "icon": "heart",
      "color": "#16A34A"
    },
    {
      "id": 5,
      "category": "Reminder",
      "title": "Appointment Reminder",
      "message": "Hi {name}, this is a reminder for your appointment at {business_name} on <date> at <time>. See you soon!",
      "variables": ["name", "business_name",],
      "tags": ["reminder", "appointment"],
      "icon": "clock",
      "color": "#D97706"
    },
    {
      "id": 6,
      "category": "Reminder",
      "title": "Payment Due Reminder",
      "message": "Hi {name}, a friendly reminder that your payment of {amount} is due on {due_date}. Thank you!",
      "variables": ["name", "amount", "due_date"],
      "tags": ["reminder", "payment"],
      "icon": "clock",
      "color": "#D97706"
    },
    {
      "id": 7,
      "category": "Promo",
      "title": "Flash Sale",
      "message": "⚡ Flash Sale Alert! Get <discount>% off at {business_name} today only. Use code <promo_code> at checkout.",
      "variables": ["business_name"],
      "tags": ["promo", "sale"],
      "icon": "tag",
      "color": "#DC2626"
    },
    {
      "id": 8,
      "category": "Promo",
      "title": "Seasonal Discount",
      "message": "🎄 {business_name} Holiday Sale is here! Enjoy <discount>% off sitewide until <end_date>.",
      "variables": ["business_name"],
      "tags": ["promo", "seasonal"],
      "icon": "tag",
      "color": "#DC2626"
    },
    {
      "id": 9,
      "category": "Birthday",
      "title": "Birthday Greeting",
      "message": "Happy Birthday, {name}! 🎂 From all of us at {business_name}, enjoy a special <discount>% off gift just for you.",
      "variables": ["name", "business_name", "discount"],
      "tags": ["birthday", "greeting"],
      "icon": "cake",
      "color": "#DB2777"
    },
    {
      "id": 10,
      "category": "Announcement",
      "title": "New Product Launch",
      "message": "📢 Exciting news, {name}! {business_name} just launched {product_name}. Check it out now!",
      "variables": ["name", "business_name", "product_name"],
      "tags": ["announcement", "product"],
      "icon": "megaphone",
      "color": "#0891B2"
    },
    {
      "id": 11,
      "category": "Announcement",
      "title": "Store Hours Update",
      "message": "Hi {name}, please note that {business_name} will be {status} on <date>. Thank you for understanding.",
      "variables": ["name", "business_name", "status"],
      "tags": ["announcement", "hours"],
      "icon": "megaphone",
      "color": "#0891B2"
    },
    {
      "id": 12,
      "category": "Feedback",
      "title": "Request Review",
      "message": "Hi {name}, we'd love to hear about your experience at {business_name}! Leave us a review here: {review_link}",
      "variables": ["name", "business_name", "review_link"],
      "tags": ["feedback", "review"],
      "icon": "star",
      "color": "#7C3AED"
    },
    {
      "id": 13,
      "category": "Win-back",
      "title": "We Miss You",
      "message": "We miss you, {name}! 💔 It's been a while — come back to {business_name} and enjoy <discount>% off your next order.",
      "variables": ["name", "business_name", "discount"],
      "tags": ["win-back", "re-engagement"],
      "icon": "refresh",
      "color": "#EA580C"
    }
]

export default templatesDefault