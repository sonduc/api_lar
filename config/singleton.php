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

    /**
     * Bookings Binding
     * @model Booking
     *        BookingStatus
     */
    \App\Repositories\Bookings\Booking::class        => [
        \App\Repositories\Bookings\BookingRepositoryInterface::class,
        \App\Repositories\Bookings\BookingRepository::class,
    ],
    \App\Repositories\Bookings\BookingStatus::class  => [
        \App\Repositories\Bookings\BookingStatusRepositoryInterface::class,
        \App\Repositories\Bookings\BookingStatusRepository::class,
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
     * Blogs Binding
     */
    \App\Repositories\Blogs\Blog::class => [
        \App\Repositories\Blogs\BlogRepositoryInterface::class,
        \App\Repositories\Blogs\BlogRepository::class,
    ],
    \App\Repositories\Blogs\BlogTranslate::class => [
        \App\Repositories\Blogs\BlogTranslateRepositoryInterface::class,
        \App\Repositories\Blogs\BlogTranslateRepository::class,
    ],
    \App\Repositories\Blogs\Tag::class => [
        \App\Repositories\Blogs\TagRepositoryInterface::class,
        \App\Repositories\Blogs\TagRepository::class,
    ]
];
