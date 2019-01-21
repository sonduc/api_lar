<?php

return [
    /**
     * Users binding
     * @model User
     */
    \App\User::class                                 => [
        \App\Repositories\Users\UserRepositoryInterface::class,
        \App\Repositories\Users\UserRepository::class,
    ],

    /**
     * Room binding
     * @model Room
     *        RoomMedia
     *        RoomTimeBlock
     *        RoomTranslate
     *        RoomOptionalPrice
     */
    \App\Repositories\Rooms\Room::class              => [
        \App\Repositories\Rooms\RoomRepositoryInterface::class,
        \App\Repositories\Rooms\RoomRepository::class,
    ],
    \App\Repositories\Rooms\RoomMedia::class         => [
        \App\Repositories\Rooms\RoomMediaRepositoryInterface::class,
        \App\Repositories\Rooms\RoomMediaRepository::class,
    ],
    \App\Repositories\Rooms\RoomTimeBlock::class     => [
        \App\Repositories\Rooms\RoomTimeBlockRepositoryInterface::class,
        \App\Repositories\Rooms\RoomTimeBlockRepository::class,
    ],
    \App\Repositories\Rooms\RoomTranslate::class     => [
        \App\Repositories\Rooms\RoomTranslateRepositoryInterface::class,
        \App\Repositories\Rooms\RoomTranslateRepository::class,
    ],
    \App\Repositories\Rooms\RoomOptionalPrice::class => [
        \App\Repositories\Rooms\RoomOptionalPriceRepositoryInterface::class,
        \App\Repositories\Rooms\RoomOptionalPriceRepository::class,
    ],
    \App\Repositories\Rooms\RoomReview::class => [
        \App\Repositories\Rooms\RoomReviewRepositoryInterface::class,
        \App\Repositories\Rooms\RoomReviewRepository::class,
    ],

    /**
     * Bookings Binding
     * @model Booking
     *        BookingStatus
     *        BookingCancel
     */
    \App\Repositories\Bookings\Booking::class        => [
        \App\Repositories\Bookings\BookingRepositoryInterface::class,
        \App\Repositories\Bookings\BookingRepository::class,
    ],
    \App\Repositories\Bookings\BookingStatus::class  => [
        \App\Repositories\Bookings\BookingStatusRepositoryInterface::class,
        \App\Repositories\Bookings\BookingStatusRepository::class,
    ],
    \App\Repositories\Bookings\BookingCancel::class  => [
        \App\Repositories\Bookings\BookingCancelRepositoryInterface::class,
        \App\Repositories\Bookings\BookingCancelRepository::class,
    ],


    /**
     * Payments Binding
     * @model PaymentHistory
     */
    \App\Repositories\Payments\PaymentHistory::class => [
        \App\Repositories\Payments\PaymentHistoryRepositoryInterface::class,
        \App\Repositories\Payments\PaymentHistoryRepository::class,
    ],

    /**
     * Blog binding
     */
    \App\Repositories\Blogs\Blog::class              => [
        \App\Repositories\Blogs\BlogRepositoryInterface::class,
        \App\Repositories\Blogs\BlogRepository::class,
    ],

    \App\Repositories\Blogs\BlogTranslate::class => [
        \App\Repositories\Blogs\BlogTranslateRepositoryInterface::class,
        \App\Repositories\Blogs\BlogTranslateRepository::class,
    ],

    \App\Repositories\Blogs\Tag::class                    => [
        \App\Repositories\Blogs\TagRepositoryInterface::class,
        \App\Repositories\Blogs\TagRepository::class,
    ],

    /**
     * Blog binding
     */
    \App\Repositories\Categories\Category::class          => [
        \App\Repositories\Categories\CategoryRepositoryInterface::class,
        \App\Repositories\Categories\CategoryRepository::class,
    ],
    \App\Repositories\Categories\CategoryTranslate::class => [
        \App\Repositories\Categories\CategoryTranslateRepositoryInterface::class,
        \App\Repositories\Categories\CategoryTranslateRepository::class,
    ],
    /**
     * Collection binding
     */
    \App\Repositories\Collections\Collection::class       => [
        \App\Repositories\Collections\CollectionRepositoryInterface::class,
        \App\Repositories\Collections\CollectionRepository::class,
    ],

    \App\Repositories\Collections\CollectionTranslate::class => [
        \App\Repositories\Collections\ColectionTranslateRepositoryInterface::class,
        \App\Repositories\Collections\CollectionTranslateRepository::class,
    ],

    /**
     * Promotion binding
     */
    \App\Repositories\Promotions\Promotion::class => [
        \App\Repositories\Promotions\PromotionRepositoryInterface::class,
        \App\Repositories\Promotions\PromotionRepository::class,
    ],

    /**
     * Coupon binding
     */
    \App\Repositories\Coupons\Coupon::class => [
        \App\Repositories\Coupons\CouponRepositoryInterface::class,
        \App\Repositories\Coupons\CouponRepository::class,
    ],

    /**
     * City binding
     */
    \App\Repositories\Cities\City::class => [
        \App\Repositories\Cities\CityRepositoryInterface::class,
        \App\Repositories\Cities\CityRepository::class,
    ],


    /**
     * District binding
     */
    \App\Repositories\Districts\District::class => [
        \App\Repositories\Districts\DistrictRepositoryInterface::class,
        \App\Repositories\Districts\DistrictRepository::class,
    ],

    /**
     * EmailCustomer binding
     */
    App\Repositories\EmailCustomers\EmailCustomer::class => [
        App\Repositories\EmailCustomers\EmailCustomersRepositoryInterface::class,
        App\Repositories\EmailCustomers\EmailCustomersRepository::class,
    ],

    /**
     * GuidebookCategory binding
     */
    App\Repositories\GuidebookCategories\GuidebookCategory::class => [
        App\Repositories\GuidebookCategories\GuidebookCategoryRepositoryInterface::class,
        App\Repositories\GuidebookCategories\GuidebookCategoryRepository::class,
    ],

    /**
     * Place binding
     */
    App\Repositories\Places\Place::class => [
        App\Repositories\Places\PlaceRepositoryInterface::class,
        App\Repositories\Places\PlaceRepository::class,
    ],
    /**
     * Wish-List binding
     */
    \App\Repositories\WishLists\WishList::class => [
        \App\Repositories\WishLists\WishListRepositoryInterface::class,
        \App\Repositories\WishLists\WishListRepository::class,
    ],

    /**
     * Statistical binding
     */
    \App\Repositories\Statisticals\Statistical::class => [
        \App\Repositories\Statisticals\StatisticalRepositoryInterface::class,
        \App\Repositories\Statisticals\StatisticalRepository::class,
    ],

    /**
     * Room Calendar binding
     */
    \App\Repositories\Roomcalendars\RoomCalendar::class => [
        \App\Repositories\Roomcalendars\RoomCalendarRepositoryInterface::class,
        \App\Repositories\Roomcalendars\RoomCalendarRepository::class,
    ],

    /**
     * Referral binding
     */
    \App\Repositories\Referrals\Referral::class => [
        \App\Repositories\Referrals\ReferralRepositoryInterface::class,
        \App\Repositories\Referrals\ReferralRepository::class,
    ],
    /**
     * Comfort
     */
    \App\Repositories\Comforts\Comfort::class => [
        \App\Repositories\Comforts\ComfortRepositoryInterface::class,
        \App\Repositories\Comforts\ComfortRepository::class,
    ],

    /**
     * ComfortTranslate
     */
    \App\Repositories\Comforts\ComfortTranslate::class => [
        \App\Repositories\Comforts\ComfortTranslateRepositoryInterface::class,
        \App\Repositories\Comforts\ComfortTranslateRepository::class,
    ],

    /**
     * Transactions Type
     */
    \App\Repositories\TransactionTypes\TransactionType::class => [
        \App\Repositories\TransactionTypes\TransactionTypeRepositoryInterface::class,
        \App\Repositories\TransactionTypes\TransactionTypeRepository::class,
    ],

    /**
     * Transactions
     */
    \App\Repositories\Transactions\Transaction::class => [
        \App\Repositories\Transactions\TransactionRepositoryInterface::class,
        \App\Repositories\Transactions\TransactionRepository::class,
    ],

    /**
     * Compare Checking
     */
    App\Repositories\CompareCheckings\CompareChecking::class => [
        App\Repositories\CompareCheckings\CompareCheckingRepositoryInterface::class,
        App\Repositories\CompareCheckings\CompareCheckingRepository::class,
    ],


    /**
     * Bao Kim Trade
     */
    App\Repositories\Bao_Kim_Trade_History\BaoKimTradeHistory::class => [
        App\Repositories\Bao_Kim_Trade_History\BaoKimTradeHistoryRepositoryInterface::class,
        App\Repositories\Bao_Kim_Trade_History\BaoKimTradeHistoryRepository::class,
    ],

    /**
     * Settings
     */
    App\Repositories\Settings\Setting::class => [
        App\Repositories\Settings\SettingRepositoryInterface::class,
        App\Repositories\Settings\SettingRepository::class,
    ],


    /**
     * SubTopic
     */
    App\Repositories\SubTopic\SubTopic::class => [
        App\Repositories\SubTopic\SubTopicRepositoryInterface::class,
        App\Repositories\SubTopic\SubTopicRepository::class,
    ],

    /**
     * Ticket
     */
    App\Repositories\Ticket\Ticket::class => [
        App\Repositories\Ticket\TicketRepositoryInterface::class,
        App\Repositories\Ticket\TicketRepository::class,
    ],

    /**
     * Topic
     */
    App\Repositories\Topic\Topic::class => [
        App\Repositories\Topic\TopicRepositoryInterface::class,
        App\Repositories\Topic\TopicRepository::class,
    ],

    /**
     * Comment Ticket
     */
    App\Repositories\CommentTicket\CommentTicket::class => [
        App\Repositories\CommentTicket\CommentTicketRepositoryInterafae::class,
        App\Repositories\CommentTicket\CommentTicketRepository::class,
    ],


];
