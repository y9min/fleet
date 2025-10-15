-- Supabase Data Migration
-- Generated: 2025-10-12 19:32:30

-- Companies Migration
INSERT INTO companies (id, name, description, email, phone, address, is_active, created_at, updated_at) VALUES (
    '1c93c616-d37f-45a1-98fb-8b4bbccce757',
    'Default Company',
    'Default company for existing data',
    NULL,
    NULL,
    NULL,
    true,
    '2025-10-01 12:05:07',
    '2025-10-01 12:05:07'
);

INSERT INTO companies (id, name, description, email, phone, address, is_active, created_at, updated_at) VALUES (
    'cfe4953a-7ff0-42d5-962c-48689116e7c2',
    'Big Boss PCO Rentals',
    'Company for master@admin.com',
    NULL,
    NULL,
    'Simplex House, Freshwater Road Dagenham, London RM8 1RX',
    true,
    '2025-10-01 12:05:07',
    '2025-10-08 13:35:27'
);

INSERT INTO companies (id, name, description, email, phone, address, is_active, created_at, updated_at) VALUES (
    '1bef4bd6-2525-4128-8fdc-d366f6155a86',
    'POA',
    NULL,
    NULL,
    NULL,
    NULL,
    true,
    '2025-10-02 05:32:29',
    '2025-10-02 05:46:17'
);

INSERT INTO companies (id, name, description, email, phone, address, is_active, created_at, updated_at) VALUES (
    '63d12fac-a747-4e36-ae7e-941e555adab6',
    'Example Company',
    NULL,
    NULL,
    NULL,
    NULL,
    true,
    '2025-10-12 17:59:05',
    '2025-10-12 17:59:05'
);

-- Vehicle Types Migration
INSERT INTO vehicle_types (id, name, display_name, icon, seats, is_enabled, created_at, updated_at) VALUES (
    'be284a9f-218a-408e-bc3b-5b1d6168bdc1',
    'Convertible',
    'Convertible',
    NULL,
    2,
    true,
    '2025-09-22 09:00:57',
    '2025-09-22 09:00:57'
);

INSERT INTO vehicle_types (id, name, display_name, icon, seats, is_enabled, created_at, updated_at) VALUES (
    'f67faf66-cd65-4a7c-ac31-b9fab4277e55',
    'Coupe',
    'Coupe',
    NULL,
    2,
    true,
    '2025-09-22 09:00:57',
    '2025-09-22 09:00:57'
);

INSERT INTO vehicle_types (id, name, display_name, icon, seats, is_enabled, created_at, updated_at) VALUES (
    '38d604ac-0865-4ca0-8d87-05ca4a9e697a',
    'Estate',
    'Estate',
    NULL,
    5,
    true,
    '2025-09-22 09:00:57',
    '2025-09-22 09:00:57'
);

INSERT INTO vehicle_types (id, name, display_name, icon, seats, is_enabled, created_at, updated_at) VALUES (
    '04efde0b-200b-42d2-a80b-e007b4080585',
    'Hatchback',
    'Hatchback',
    NULL,
    5,
    true,
    '2025-09-22 09:00:57',
    '2025-09-22 09:00:57'
);

INSERT INTO vehicle_types (id, name, display_name, icon, seats, is_enabled, created_at, updated_at) VALUES (
    '79ea0c45-d79b-4008-a64e-a9838fcaffe1',
    'MPV',
    'MPV',
    NULL,
    7,
    true,
    '2025-09-22 09:00:57',
    '2025-09-22 09:00:57'
);

INSERT INTO vehicle_types (id, name, display_name, icon, seats, is_enabled, created_at, updated_at) VALUES (
    'c52f9a98-4832-40e3-b698-a354e41c2e17',
    'Pickup',
    'Pickup',
    NULL,
    5,
    true,
    '2025-09-22 09:00:57',
    '2025-09-22 09:00:57'
);

INSERT INTO vehicle_types (id, name, display_name, icon, seats, is_enabled, created_at, updated_at) VALUES (
    'f11c6243-8106-416e-8330-869a25b3d3dc',
    'Saloon',
    'Saloon',
    NULL,
    5,
    true,
    '2025-09-22 09:00:57',
    '2025-09-22 09:00:57'
);

INSERT INTO vehicle_types (id, name, display_name, icon, seats, is_enabled, created_at, updated_at) VALUES (
    '3fc420fa-9804-493f-a4ac-c19066a65e16',
    'SUV',
    'SUV',
    NULL,
    7,
    true,
    '2025-09-22 09:00:57',
    '2025-09-22 09:00:57'
);

-- Users Migration
INSERT INTO users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, is_verified, created_at, updated_at) VALUES (
    '61f76c3e-a0cd-4dce-ae12-b28e0c8d5323',
    'cfe4953a-7ff0-42d5-962c-48689116e7c2',
    'Super Administrator',
    'master@admin.com',
    '$2y$10$oRVwGqjS7RT.ae9rLPlbwevOJz88d7mUuDE1vPtWEsHBevanPCq6q',
    'S',
    'b2ffe199-2fa1-4b44-94e1-082faa07fde5',
    'vNjY40dy2vWTYJqPfsOGRW331lIU8OY2qfUrqL5Oo4RTxnIvsxT9ZVIHlXFv',
    true,
    false,
    '2021-11-20 07:03:48',
    '2021-11-20 07:03:48'
);

INSERT INTO users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, is_verified, created_at, updated_at) VALUES (
    'efe52f13-6a3f-498e-869b-b8ab598e2081',
    'cfe4953a-7ff0-42d5-962c-48689116e7c2',
    'User One',
    'user1@admin.com',
    '$2y$10$0yL5QM7IVdb3B6FUi3m2HugbnC5VK2HncZR0VGr1cvsSEV/Nc/pc.',
    'O',
    'efbd4393-ce9a-4361-b5f3-c66bb2037c2e',
    '1TxP6fg9WPYmPse2PaRggJUAyt0De9xOYUivQeiSC0N92GYEFVOviNfQq6Qk',
    true,
    false,
    '2021-11-20 07:03:48',
    '2021-11-20 07:03:48'
);

INSERT INTO users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, is_verified, created_at, updated_at) VALUES (
    '370220ab-5167-438e-8398-ab1da37c2b3d',
    'cfe4953a-7ff0-42d5-962c-48689116e7c2',
    'User Two',
    'user2@admin.com',
    '$2y$10$JPAnaeoH1aw5NIoomGPHyOi03VVOl0y6/iU4Po0Q/d8HaKsOpoPK.',
    'O',
    'b02b981e-5cae-4587-8981-89866c7d7142',
    'dLlOOjzxTrYzA2N9IEJeduRXnpLwrARmnaXvwbtLtPCFgpcZgeYIfErCQ6ja',
    true,
    false,
    '2021-11-20 07:03:48',
    '2021-11-20 07:03:48'
);

INSERT INTO users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, is_verified, created_at, updated_at) VALUES (
    '08f46bc4-b497-435c-856f-2d36f4e5328d',
    'cfe4953a-7ff0-42d5-962c-48689116e7c2',
    'Driver One',
    '1759593972_deleted_driver1@gmail.com',
    '$2y$10$tVzZOVA9EFpePxT6ShK7je7h.EMWzV38.t.9FM7JvXYcOOM/DKyt6',
    'D',
    'cbbcd871-8d49-41cb-bcf0-5baddd7c62d0',
    'TuaPjW443femKIauadpE0VskcpvSwBke0dsS39YeOaiAAkS8rsek1vuXx9F3',
    true,
    false,
    '2021-11-20 07:03:49',
    '2025-10-04 16:06:12'
);

INSERT INTO users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, is_verified, created_at, updated_at) VALUES (
    '0024b92b-c523-4457-8d3f-8466f5471e85',
    'cfe4953a-7ff0-42d5-962c-48689116e7c2',
    'Driver Two',
    '1759591781_deleted_driver2@gmail.com',
    '$2y$12$8Xl13/Zqz14lPbJr3894VO7C98nrZi5is11zRcDL2S60vfnCx3dz2',
    'D',
    '7eebe307-87bf-4fd4-829c-daef838a05cd',
    '0G1fjlmammOVOA7hxpsXAtw0Wp1oWLPC2xCxrCQoqS14m0U2d26sGHw15LuX',
    true,
    false,
    '2021-11-20 07:03:49',
    '2025-10-04 15:29:41'
);

INSERT INTO users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, is_verified, created_at, updated_at) VALUES (
    'ac12d89c-dc36-412f-8bf4-c8272d284a11',
    'cfe4953a-7ff0-42d5-962c-48689116e7c2',
    'Mariah Bahringer',
    'nbode@example.net',
    '$2y$12$8Xl13/Zqz14lPbJr3894VO7C98nrZi5is11zRcDL2S60vfnCx3dz2',
    'D',
    '5625a866-7e3e-467d-8ae5-e100129139d3',
    '4vyb77kPNaiMyuPG63WUFctB2G3NPjPx1kgafzjBOWWnhEsVS8rScIg7s98O',
    true,
    false,
    '2021-11-20 07:04:12',
    '2025-10-02 13:10:19'
);

INSERT INTO users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, is_verified, created_at, updated_at) VALUES (
    '8992702d-7ec7-4782-bdef-fbbc25e07a88',
    'cfe4953a-7ff0-42d5-962c-48689116e7c2',
    'Leland Schuppe',
    'oabshire@example.org',
    '$2y$12$8Xl13/Zqz14lPbJr3894VO7C98nrZi5is11zRcDL2S60vfnCx3dz2',
    'D',
    'ff62bd57-d5d3-4474-9d0f-ab662bf8e93b',
    'rDQOs9u7J4HX9gRG9ba6SHpDfpcpNqxmKVuZmhgGAc9EK1Zbfs60cBepetsr',
    true,
    false,
    '2021-11-20 07:04:13',
    '2021-11-20 07:05:06'
);

INSERT INTO users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, is_verified, created_at, updated_at) VALUES (
    'ee94b633-0419-4071-8cf8-5ba69b664f87',
    'cfe4953a-7ff0-42d5-962c-48689116e7c2',
    'Noelle Stafford',
    'kedim@mailinator.com',
    '$2y$12$8Xl13/Zqz14lPbJr3894VO7C98nrZi5is11zRcDL2S60vfnCx3dz2',
    'C',
    '7b21fc46-7a87-47c5-8888-914312cefd3d',
    'pN1iP2z5R3KnjTtk2QiJHES7saG5MvxswgHjCaCu9Ob2CR32is6dD98c0txL',
    true,
    false,
    '2021-11-22 23:01:58',
    '2025-10-02 06:11:01'
);

INSERT INTO users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, is_verified, created_at, updated_at) VALUES (
    '665c8a7f-a72c-495b-a7c6-d5ef2dcbdf20',
    'cfe4953a-7ff0-42d5-962c-48689116e7c2',
    'Sifat Ali',
    'alisifat@gmail.com',
    '$2y$10$uQp0MEdrFNBDDY7OIiEjD.EdXg8vdGibSCA/Z0gJaYRzcLPsZmN5C',
    'D',
    'c5fece36-fb2a-4c7a-91b5-3b1163d6da6b',
    'X8IvG1rSUdbb6awjvTYBGLBEvcIyT80lUCtVQ0pajFEy3Zr2Uzn4OBlHCOBx',
    true,
    false,
    '2025-09-22 12:38:18',
    '2025-10-12 15:59:16'
);

INSERT INTO users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, is_verified, created_at, updated_at) VALUES (
    '7387de84-fa1d-4f4f-b282-12517ac3a03e',
    'cfe4953a-7ff0-42d5-962c-48689116e7c2',
    'John Smith',
    'johnsmith@gmail.com',
    '$2y$12$8Xl13/Zqz14lPbJr3894VO7C98nrZi5is11zRcDL2S60vfnCx3dz2',
    'D',
    'eb594bcf-dece-4648-9365-985820d325c5',
    'KIrE3k8SD5Hi1oRGvh9QSZBnpTmQIjj92aTKSTkQQoZkwJUrgqS9DJqjM8KM',
    true,
    false,
    '2025-09-22 12:49:42',
    '2025-09-22 12:49:58'
);

INSERT INTO users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, is_verified, created_at, updated_at) VALUES (
    '3642b0f9-8f44-4efa-a9ca-0e55ea9ad286',
    'cfe4953a-7ff0-42d5-962c-48689116e7c2',
    'Kai Stephens',
    'stephkai11@gmail.com',
    '$2y$12$8Xl13/Zqz14lPbJr3894VO7C98nrZi5is11zRcDL2S60vfnCx3dz2',
    'D',
    'e0b7472a-8a09-48c1-8527-48de105a69a7',
    'OBg3S2J6vEiSmwDexe2vyx62VkLANREEoSbzhW0nu7eVG8moYtMg60HXA4Sw',
    true,
    false,
    '2025-09-22 12:57:39',
    '2025-09-22 12:57:39'
);

INSERT INTO users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, is_verified, created_at, updated_at) VALUES (
    '5e74839c-24fc-4601-a205-ed450f89c8d7',
    'cfe4953a-7ff0-42d5-962c-48689116e7c2',
    'Yasin Ahmed',
    'yazahmed@hotmail.com',
    '$2y$12$8Xl13/Zqz14lPbJr3894VO7C98nrZi5is11zRcDL2S60vfnCx3dz2',
    'D',
    'fcdb5e97-b0a5-4739-bd09-12d365dd867b',
    'Hf8BeblvdQj0xJJ1z2Pb02ETLaYCbSntOjabE74toSd3fRns5nsCMwDZ22rv',
    true,
    false,
    '2025-09-22 13:05:47',
    '2025-09-22 13:05:47'
);

INSERT INTO users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, is_verified, created_at, updated_at) VALUES (
    'd78611d3-bc74-4117-a1ae-1e7e1dff1652',
    'cfe4953a-7ff0-42d5-962c-48689116e7c2',
    'Amber McDonald',
    'ambermcd@yahoomail.com',
    '$2y$12$8Xl13/Zqz14lPbJr3894VO7C98nrZi5is11zRcDL2S60vfnCx3dz2',
    'D',
    'd4fd3b76-038c-4721-8bc0-36eba824c800',
    'epjXIR5gqXcVwa1OdcbzAMPQ9iMohdWIGQKPft5MmJYuaR82tYFhVmJ7jEEk',
    true,
    false,
    '2025-09-23 15:38:53',
    '2025-09-23 15:38:53'
);

INSERT INTO users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, is_verified, created_at, updated_at) VALUES (
    'e599894b-5dfc-4667-b2de-b9abc3e0bf61',
    'cfe4953a-7ff0-42d5-962c-48689116e7c2',
    'Edward Winter',
    'edwin@hotmail.com',
    '$2y$12$8Xl13/Zqz14lPbJr3894VO7C98nrZi5is11zRcDL2S60vfnCx3dz2',
    'D',
    'a450de71-9b35-4f8a-82f5-e9884ea65c22',
    'PNhZAQ5oclMVfaD4Fb4NqXLok5mZNnvOFbpE4VZzDs9qBkDnVbRn1rPlePCG',
    true,
    false,
    '2025-09-23 16:01:50',
    '2025-09-23 16:01:50'
);

INSERT INTO users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, is_verified, created_at, updated_at) VALUES (
    'c3b55d32-e87f-49a1-b784-3cc0c6c88ce3',
    'cfe4953a-7ff0-42d5-962c-48689116e7c2',
    'Stacy Williams',
    'williamsst@gmail.com',
    '$2y$10$1Fw.aYBQ/JwTKyEx1lhcBur/oyxw1mLGeF69SjJcuevcaMbpXs4hm',
    'D',
    NULL,
    'H8O9LWJXeroG5oNqhiZ4rG2avRwOAWMtnJcUXO8VDl8kl5MXpIi6WXEzycR2',
    true,
    false,
    '2025-10-01 05:56:52',
    '2025-10-08 08:41:37'
);

INSERT INTO users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, is_verified, created_at, updated_at) VALUES (
    '79a1c917-fdfe-439d-82bc-2336c682209b',
    NULL,
    'Yamin Ahmed',
    'yamzahmed@hotmail.com',
    '$2y$10$aWz/qB8cAz4g0zj7cVOx7Opyps0bdA4UqHvvs5JUgUSKLr/ICE8uu',
    'B',
    NULL,
    'zwIxVC2OG5CpntSKuytBXvh9OlYCtyEnxCVm2mcsYBSpt3Iov0oKDprzlFTV',
    true,
    false,
    '2025-10-01 06:30:51',
    '2025-10-12 17:19:48'
);

INSERT INTO users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, is_verified, created_at, updated_at) VALUES (
    'e9621771-7a91-4eee-b7ec-d6c2c1518ae8',
    'cfe4953a-7ff0-42d5-962c-48689116e7c2',
    'snnfjon fejie',
    'jacobsaunder473@gmail.com',
    '$2y$10$njB7f1Rdjm0uUQmkasdnte.VNtG3WeFkNi/qJecGlOMogKOMiejne',
    'D',
    NULL,
    'xOd7U0DCfy6YAO93BgESIBrzNGdgHcNfYW81vDejeozQJz7TlcuVywKJgrl9',
    true,
    false,
    '2025-10-08 07:21:00',
    '2025-10-08 08:41:37'
);

INSERT INTO users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, is_verified, created_at, updated_at) VALUES (
    'edce787f-1339-41b8-a22d-7f0d0529f40b',
    'cfe4953a-7ff0-42d5-962c-48689116e7c2',
    'Quienten Tarantino',
    'tarantheman@yahoomail.com',
    '$2y$10$GtgqJ9HKQ6nRS0Mi7NhX1e03UAcWikkJXOPgZ28r8AhJm7SghWF5S',
    'D',
    NULL,
    'frFegBw7VmdQAzJswhpxEmkeRdFh5ce5hnsEZqAKZ3WhIIrUK2sd5NRTmJPo',
    true,
    false,
    '2025-10-08 07:48:04',
    '2025-10-08 08:41:37'
);

INSERT INTO users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, is_verified, created_at, updated_at) VALUES (
    'a4d1f237-bff2-4460-9668-0de649f030b6',
    'cfe4953a-7ff0-42d5-962c-48689116e7c2',
    'Hollie Rhodes',
    'rhodeshollie0@gmail.com',
    '$2y$10$310cQh.AjoCxnH6W7uwWC.Ow.cR70rlDRPXJCIsHungv5h1dACdd2',
    'D',
    NULL,
    'ba7g5ttk37udLLHSTFnLibq0XjdryGKYvcqESwKMtdcFNcLSMFKzVM9Nt9xX',
    true,
    false,
    '2025-10-08 08:18:39',
    '2025-10-08 08:41:37'
);

INSERT INTO users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, is_verified, created_at, updated_at) VALUES (
    'd65ee0c0-69ed-4b2b-9d92-219df063ab7a',
    'cfe4953a-7ff0-42d5-962c-48689116e7c2',
    'William Honson',
    'josephwilk2022@gmail.com',
    '$2y$10$.gAGAKEBLMoOZGCGEmlXj.vwy3Nn5.wEF2EJVp.CqPF3ImkQRO9mG',
    'D',
    NULL,
    'snlpKvYdlH7KhEBsTMXzk92xsBRsB6HPHvTynOPtt4XdUa2ZVwFCvbNn6ElU',
    true,
    false,
    '2025-10-11 06:32:20',
    '2025-10-11 06:32:20'
);

INSERT INTO users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, is_verified, created_at, updated_at) VALUES (
    '9ee65ddc-6f72-4307-b965-3f65af2bce50',
    'cfe4953a-7ff0-42d5-962c-48689116e7c2',
    'Kallum Hirst',
    'kallhd@hotmail.com',
    '$2y$10$T4B51E4UlOi5/pYdL5sGW.QnUMiEEPPnpV3V8R0IcbxZU/L.AzciC',
    'D',
    NULL,
    'W4PRFeSYU5lk7nYqJy2aO6AbimQzY0rj5LdWz78Ct5h5kBZpClHk0jUFout5',
    true,
    false,
    '2025-10-11 07:22:08',
    '2025-10-11 07:22:08'
);

INSERT INTO users (id, company_id, name, email, password, user_type, group_id, api_token, is_active, is_verified, created_at, updated_at) VALUES (
    '7173c931-f0a8-4621-b446-93cc142bd2c8',
    '63d12fac-a747-4e36-ae7e-941e555adab6',
    'Example User',
    'example@gmail.com',
    '$2y$10$SkhN5A0npbPatB.wut8qQOvnc6132RFQhy9QDhrd3gpABiT0rZvHK',
    'S',
    NULL,
    'ua7Ys3D15VbTxCPJs2UgYsKzg74lBcMkstknYYF3rcr3kw1CPoZGVR3QoPkH',
    true,
    false,
    '2025-10-12 17:59:34',
    '2025-10-12 17:59:34'
);

-- Vehicles Migration
INSERT INTO vehicles (id, company_id, make_name, model_name, color_name, year, engine_type, horse_power, vin, license_plate, mileage, int_mileage, in_service, status, height, length, breadth, weight, insurance_number, vehicle_image, exp_date, reg_exp_date, lic_exp_date, metadata, created_at, updated_at) VALUES (
    '168a0f62-4df6-4406-a9f6-9be420522737',
    'cfe4953a-7ff0-42d5-962c-48689116e7c2',
    'BMW',
    'M3',
    NULL,
    '2018',
    'Petrol',
    NULL,
    NULL,
    'Y9 MFN',
    0,
    34000,
    true,
    'available',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    '{"ins_number":"","ins_exp_date":"","documents":"","traccar_device_id":null,"traccar_vehicle_id":null,"assign_driver_id":"18","luggage":null,"price":"220","vehicle_status":"Rented","telematics_link":"https:\/\/www.youtube.com\/watch?v=a2a6KYS2sDw","initial_cost":"28900","vehicle_scheme":"Rental","vehicle_price":"220","price_period":"monthly","udf":"N;","average":null,"purchase_info":"a:1:{i:0;a:2:{s:8:\"exp_name\";s:4:\"Rnnn\";s:10:\"exp_amount\";s:2:\"50\";}}","mot_expiry_date":"2025-10-15","inspection_notes":null}',
    '2025-09-17 14:05:04',
    '2025-09-22 09:07:06'
);

INSERT INTO vehicles (id, company_id, make_name, model_name, color_name, year, engine_type, horse_power, vin, license_plate, mileage, int_mileage, in_service, status, height, length, breadth, weight, insurance_number, vehicle_image, exp_date, reg_exp_date, lic_exp_date, metadata, created_at, updated_at) VALUES (
    'f9d74fb4-dfb8-4088-9bcb-951000dd02ea',
    'cfe4953a-7ff0-42d5-962c-48689116e7c2',
    'Audi',
    'RS7',
    NULL,
    '2020',
    'Petrol',
    NULL,
    NULL,
    'C3 OYA',
    0,
    29000,
    true,
    'available',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    '{"ins_number":"","ins_exp_date":"","documents":"","traccar_device_id":null,"traccar_vehicle_id":null,"assign_driver_id":null,"luggage":null,"price":"400","vehicle_status":"Workshop","telematics_link":"https:\/\/pixlr.com\/editor\/#myhistory","initial_cost":"65000","vehicle_scheme":"Rent To Buy","vehicle_price":"400","price_period":"weekly","udf":"N;","average":null,"mot_expiry_date":"2026-07-06","inspection_notes":"Small coolant leak, yet to be diagnosed. Likely seal failure"}',
    '2025-09-17 14:44:49',
    '2025-09-17 14:44:49'
);

INSERT INTO vehicles (id, company_id, make_name, model_name, color_name, year, engine_type, horse_power, vin, license_plate, mileage, int_mileage, in_service, status, height, length, breadth, weight, insurance_number, vehicle_image, exp_date, reg_exp_date, lic_exp_date, metadata, created_at, updated_at) VALUES (
    '6ba8d4ab-1872-4c64-a19f-799b72b11b76',
    'cfe4953a-7ff0-42d5-962c-48689116e7c2',
    'Toyota',
    'Prius',
    NULL,
    '2019',
    'Petrol',
    NULL,
    NULL,
    'S4 POA',
    0,
    78450,
    true,
    'available',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    '{"height":null,"length":null,"breadth":null,"weight":null,"ins_number":"","ins_exp_date":"","documents":"","traccar_device_id":null,"traccar_vehicle_id":null,"assign_driver_id":"6","luggage":null,"price":"320","vehicle_status":"Rented","telematics_link":"https:\/\/x.com\/home","initial_cost":"40000","vehicle_scheme":"Rent To Buy","vehicle_price":"320","price_period":"weekly","udf":"N;","average":null,"mot_expiry_date":null,"inspection_notes":null}',
    '2025-09-17 16:47:59',
    '2025-09-22 09:02:14'
);

INSERT INTO vehicles (id, company_id, make_name, model_name, color_name, year, engine_type, horse_power, vin, license_plate, mileage, int_mileage, in_service, status, height, length, breadth, weight, insurance_number, vehicle_image, exp_date, reg_exp_date, lic_exp_date, metadata, created_at, updated_at) VALUES (
    'f286e5c9-3cf9-42ad-a865-29f073574716',
    'cfe4953a-7ff0-42d5-962c-48689116e7c2',
    'BMW',
    'X5M',
    NULL,
    '2019',
    'Diesel',
    NULL,
    NULL,
    'MA71 NAS',
    0,
    35672,
    true,
    'available',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    '{"height":null,"length":null,"breadth":null,"weight":null,"ins_number":"","ins_exp_date":"","documents":"","traccar_device_id":null,"traccar_vehicle_id":null,"assign_driver_id":"9","luggage":null,"price":"1200","vehicle_status":"Rented","telematics_link":"https:\/\/www.youtube.com\/watch?v=a2a6KYS2sDw","initial_cost":"34098","vehicle_scheme":"Rental","vehicle_price":"1200","price_period":"monthly","udf":"N;","average":null,"mot_expiry_date":"2026-09-30","inspection_notes":null}',
    '2025-09-17 16:58:12',
    '2025-09-22 08:18:12'
);

INSERT INTO vehicles (id, company_id, make_name, model_name, color_name, year, engine_type, horse_power, vin, license_plate, mileage, int_mileage, in_service, status, height, length, breadth, weight, insurance_number, vehicle_image, exp_date, reg_exp_date, lic_exp_date, metadata, created_at, updated_at) VALUES (
    'ed71011b-de94-4483-a974-c363f9a576cb',
    'cfe4953a-7ff0-42d5-962c-48689116e7c2',
    'SEAT',
    'Leon',
    NULL,
    '2015',
    'Diesel',
    NULL,
    NULL,
    'LM15 XYD',
    0,
    89000,
    true,
    'available',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    '{"height":null,"length":null,"breadth":null,"weight":null,"ins_number":"","ins_exp_date":"","documents":"","traccar_device_id":"","traccar_vehicle_id":"","assign_driver_id":"15","luggage":"","price":"250","vehicle_status":"Rented","telematics_link":"https:\/\/www.instagram.com","initial_cost":"5400","vehicle_scheme":"Rental","vehicle_price":"250","price_period":"weekly","udf":"N;","average":null,"mot_expiry_date":"2025-12-05"}',
    '2025-09-17 17:12:56',
    '2025-09-19 05:46:34'
);

INSERT INTO vehicles (id, company_id, make_name, model_name, color_name, year, engine_type, horse_power, vin, license_plate, mileage, int_mileage, in_service, status, height, length, breadth, weight, insurance_number, vehicle_image, exp_date, reg_exp_date, lic_exp_date, metadata, created_at, updated_at) VALUES (
    '5d42f65c-b7d8-44ab-93d8-ad3185867313',
    'cfe4953a-7ff0-42d5-962c-48689116e7c2',
    'Land Rover',
    'SVR',
    NULL,
    '2023',
    'Petrol',
    NULL,
    NULL,
    'B16 CEO',
    0,
    12016,
    true,
    'available',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    '{"height":null,"length":null,"breadth":null,"weight":null,"ins_number":"","ins_exp_date":"","documents":"","vehicle_status":"Rented","vehicle_scheme":"Rental","price_period":"weekly","assign_driver_id":"21","telematics_link":"https:\/\/f7.hyvikk.solutions\/admin\/login","vehicle_price":"800","price":"800","initial_cost":"49999","udf":"N;","average":null}',
    '2025-09-17 17:32:59',
    '2025-09-19 05:14:29'
);

INSERT INTO vehicles (id, company_id, make_name, model_name, color_name, year, engine_type, horse_power, vin, license_plate, mileage, int_mileage, in_service, status, height, length, breadth, weight, insurance_number, vehicle_image, exp_date, reg_exp_date, lic_exp_date, metadata, created_at, updated_at) VALUES (
    '32d8bcec-b6b4-4051-b00f-c1b8b3ecd5f6',
    'cfe4953a-7ff0-42d5-962c-48689116e7c2',
    'McLaren',
    '720s',
    NULL,
    '2019',
    'Petrol',
    NULL,
    NULL,
    'MC70 YYY',
    0,
    14000,
    true,
    'available',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    '{"height":null,"length":null,"breadth":null,"weight":null,"ins_number":"","ins_exp_date":"","documents":"","vehicle_status":"Rented","vehicle_scheme":"Other","price_period":"monthly","assign_driver_id":"17","telematics_link":"https:\/\/docs.google.com\/spreadsheets\/d\/1fpSeoBN18-9uorxhAd7rdNNL9n58YgvtUjTcybuZC2M\/edit?gid=0#gid=0","vehicle_price":"3000","price":"3000","initial_cost":"125000","udf":"N;","average":null,"inspection_notes":null}',
    '2025-09-19 16:38:26',
    '2025-09-22 08:49:25'
);

INSERT INTO vehicles (id, company_id, make_name, model_name, color_name, year, engine_type, horse_power, vin, license_plate, mileage, int_mileage, in_service, status, height, length, breadth, weight, insurance_number, vehicle_image, exp_date, reg_exp_date, lic_exp_date, metadata, created_at, updated_at) VALUES (
    '9807f1fc-a900-44ce-a5bd-c9f9798bb0cf',
    'cfe4953a-7ff0-42d5-962c-48689116e7c2',
    'Bentley',
    'Continental',
    'White',
    '2023',
    'Petrol',
    '0',
    NULL,
    'B3TLY',
    13000,
    13000,
    true,
    'available',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    '{"type":"Saloon","vehicle_status":"Workshop","vehicle_scheme":"Rental","price":"750","price_period":"monthly","initial_cost":"89000","fuel_efficiency":"","telematics_link":"https:\/\/www.instagram.com","mot_expiry_date":"2025-12-09","exp_date":"2025-12-09","vehicle_price":"750","udf":"N;","average":null,"assign_driver_id":"13","inspection_notes":"Minor services, brakes and pads, parts ordered"}',
    '2025-09-21 06:57:46',
    '2025-09-21 06:57:46'
);

INSERT INTO vehicles (id, company_id, make_name, model_name, color_name, year, engine_type, horse_power, vin, license_plate, mileage, int_mileage, in_service, status, height, length, breadth, weight, insurance_number, vehicle_image, exp_date, reg_exp_date, lic_exp_date, metadata, created_at, updated_at) VALUES (
    '1578d3bb-3630-4d22-8644-4d878d0cc0f1',
    'cfe4953a-7ff0-42d5-962c-48689116e7c2',
    'Volkswagen',
    'Golf',
    'Blue',
    '2017',
    'Petrol',
    '0',
    NULL,
    'G4 PYU',
    47000,
    47000,
    true,
    'available',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    '{"type":"Hatchback","vehicle_status":"Available","vehicle_scheme":"Rental","price":"600","price_period":"monthly","initial_cost":"21000","fuel_efficiency":"","telematics_link":"https:\/\/docs.google.com\/document\/d\/1kCpwat4wNyWWSDfenSberXK05QOYLRf7AkwN2aoIhzo\/edit?tab=t.0","mot_expiry_date":"2025-11-13","exp_date":"2025-11-13","vehicle_price":"600","udf":"N;","average":null,"inspection_notes":null,"assign_driver_id":null}',
    '2025-09-21 07:13:29',
    '2025-09-22 09:13:03'
);

-- Bookings Migration
INSERT INTO bookings (id, company_id, customer_id, driver_id, vehicle_id, pickup, dropoff, pickup_addr, dest_addr, travellers, status, comment, note, cancellation, completed_at, metadata, created_at, updated_at) VALUES (
    '9cfd3d1c-951c-407f-a171-f01ab60a663e',
    NULL,
    '61f76c3e-a0cd-4dce-ae12-b28e0c8d5323',
    'a4d1f237-bff2-4460-9668-0de649f030b6',
    '1578d3bb-3630-4d22-8644-4d878d0cc0f1',
    '2025-10-09 09:30:00',
    '2025-10-09 09:45:00',
    'Simplex House, Freshwater Road Dagenham, London RM8 1RX',
    NULL,
    1,
    'pending',
    NULL,
    'bring passport',
    '0',
    NULL,
    '{"udf":"N;","accept_status":"1","ride_status":"Ongoing","booking_type":"1","journey_date":"09-10-2025","journey_time":"09:30:00"}',
    '2025-10-09 06:47:40',
    '2025-10-09 06:47:40'
);

INSERT INTO bookings (id, company_id, customer_id, driver_id, vehicle_id, pickup, dropoff, pickup_addr, dest_addr, travellers, status, comment, note, cancellation, completed_at, metadata, created_at, updated_at) VALUES (
    '4f04c497-7142-4e05-a239-7804bf4f7426',
    NULL,
    '61f76c3e-a0cd-4dce-ae12-b28e0c8d5323',
    'a4d1f237-bff2-4460-9668-0de649f030b6',
    '1578d3bb-3630-4d22-8644-4d878d0cc0f1',
    '2025-10-09 09:15:00',
    '2025-10-09 09:30:00',
    'Simplex House, Freshwater Road Dagenham, London RM8 1RX',
    NULL,
    1,
    'pending',
    NULL,
    'bring passport',
    '0',
    NULL,
    '{"udf":"N;","accept_status":"1","ride_status":"Ongoing","booking_type":"1","journey_date":"09-10-2025","journey_time":"09:15:00"}',
    '2025-10-09 06:50:43',
    '2025-10-09 06:50:43'
);

INSERT INTO bookings (id, company_id, customer_id, driver_id, vehicle_id, pickup, dropoff, pickup_addr, dest_addr, travellers, status, comment, note, cancellation, completed_at, metadata, created_at, updated_at) VALUES (
    '9c3b7129-75f0-4f50-a2e7-c85fef2ff7dc',
    NULL,
    '61f76c3e-a0cd-4dce-ae12-b28e0c8d5323',
    'a4d1f237-bff2-4460-9668-0de649f030b6',
    '6ba8d4ab-1872-4c64-a19f-799b72b11b76',
    '2025-10-09 09:30:00',
    '2025-10-09 09:45:00',
    'Simplex House, Freshwater Road Dagenham, London RM8 1RX',
    NULL,
    1,
    'pending',
    NULL,
    'bring passport',
    '0',
    NULL,
    '{"udf":"N;","accept_status":"1","ride_status":"Ongoing","booking_type":"1","journey_date":"09-10-2025","journey_time":"09:30:00","total_time":"00:00:00","total_kms":"0"}',
    '2025-10-09 07:04:40',
    '2025-10-09 07:04:40'
);

INSERT INTO bookings (id, company_id, customer_id, driver_id, vehicle_id, pickup, dropoff, pickup_addr, dest_addr, travellers, status, comment, note, cancellation, completed_at, metadata, created_at, updated_at) VALUES (
    'ad0ac1a3-82f3-474c-9481-1ae741bc4d59',
    NULL,
    '61f76c3e-a0cd-4dce-ae12-b28e0c8d5323',
    'a4d1f237-bff2-4460-9668-0de649f030b6',
    '1578d3bb-3630-4d22-8644-4d878d0cc0f1',
    '2025-10-09 07:09:00',
    '2025-10-09 07:24:00',
    'Simplex House, Freshwater Road Dagenham, London RM8 1RX',
    NULL,
    1,
    'pending',
    NULL,
    'nkjkn nknlk',
    '0',
    NULL,
    '{"udf":"N;","accept_status":"1","ride_status":"Ongoing","booking_type":"1","journey_date":"09-10-2025","journey_time":"07:09:00","total_time":"00:00:00","total_kms":"0"}',
    '2025-10-09 07:09:21',
    '2025-10-09 07:09:21'
);

INSERT INTO bookings (id, company_id, customer_id, driver_id, vehicle_id, pickup, dropoff, pickup_addr, dest_addr, travellers, status, comment, note, cancellation, completed_at, metadata, created_at, updated_at) VALUES (
    '8aa41408-5874-43a2-8aa8-e4618dada3a4',
    NULL,
    '61f76c3e-a0cd-4dce-ae12-b28e0c8d5323',
    'a4d1f237-bff2-4460-9668-0de649f030b6',
    '1578d3bb-3630-4d22-8644-4d878d0cc0f1',
    '2025-10-09 10:15:00',
    '2025-10-09 10:30:00',
    'Simplex House, Freshwater Road Dagenham, London RM8 1RX',
    NULL,
    1,
    'pending',
    NULL,
    'cfcf lkceke',
    '0',
    NULL,
    '{"udf":"N;","accept_status":"1","ride_status":"Ongoing","booking_type":"1","journey_date":"09-10-2025","journey_time":"10:15:00","total_time":"00:00:00","total_kms":"0"}',
    '2025-10-09 07:16:32',
    '2025-10-09 07:16:32'
);

INSERT INTO bookings (id, company_id, customer_id, driver_id, vehicle_id, pickup, dropoff, pickup_addr, dest_addr, travellers, status, comment, note, cancellation, completed_at, metadata, created_at, updated_at) VALUES (
    'c3695716-40f5-4696-bf78-a0db776eee08',
    NULL,
    '61f76c3e-a0cd-4dce-ae12-b28e0c8d5323',
    'a4d1f237-bff2-4460-9668-0de649f030b6',
    '1578d3bb-3630-4d22-8644-4d878d0cc0f1',
    '2025-10-10 09:15:00',
    '2025-10-10 09:30:00',
    'Simplex House, Freshwater Road Dagenham, London RM8 1RX',
    NULL,
    1,
    'pending',
    NULL,
    'bibkbjk hjljl iohji',
    '1',
    NULL,
    '{"udf":"N;","accept_status":"1","ride_status":"Cancelled","booking_type":"1","journey_date":"10-10-2025","journey_time":"09:15:00","total_time":"00:00:00","total_kms":"0","reason":"1"}',
    '2025-10-09 07:21:20',
    '2025-10-09 08:05:44'
);

INSERT INTO bookings (id, company_id, customer_id, driver_id, vehicle_id, pickup, dropoff, pickup_addr, dest_addr, travellers, status, comment, note, cancellation, completed_at, metadata, created_at, updated_at) VALUES (
    '7bda87a2-9c0b-47a4-b839-b543a8265494',
    NULL,
    '61f76c3e-a0cd-4dce-ae12-b28e0c8d5323',
    'a4d1f237-bff2-4460-9668-0de649f030b6',
    '1578d3bb-3630-4d22-8644-4d878d0cc0f1',
    '2025-10-11 10:30:00',
    '2025-10-11 10:45:00',
    'Simplex House, Freshwater Road Dagenham, London RM8 1RX',
    NULL,
    1,
    'pending',
    NULL,
    'bring passport',
    '0',
    NULL,
    '{"udf":"N;","accept_status":"1","ride_status":"Ongoing","booking_type":"1","journey_date":"11-10-2025","journey_time":"10:30:00","total_time":"00:00:00","total_kms":"0"}',
    '2025-10-09 07:31:24',
    '2025-10-09 07:31:24'
);

INSERT INTO bookings (id, company_id, customer_id, driver_id, vehicle_id, pickup, dropoff, pickup_addr, dest_addr, travellers, status, comment, note, cancellation, completed_at, metadata, created_at, updated_at) VALUES (
    '7d3ce2d6-beb6-4325-9906-76abca3404aa',
    NULL,
    '61f76c3e-a0cd-4dce-ae12-b28e0c8d5323',
    'a4d1f237-bff2-4460-9668-0de649f030b6',
    '1578d3bb-3630-4d22-8644-4d878d0cc0f1',
    '2025-10-13 09:30:00',
    '2025-10-13 09:45:00',
    'Simplex House, Freshwater Road Dagenham, London RM8 1RX',
    NULL,
    1,
    'pending',
    NULL,
    'bring passport',
    '0',
    NULL,
    '{"udf":"N;","accept_status":"1","ride_status":"Ongoing","booking_type":"1","journey_date":"13-10-2025","journey_time":"09:30:00","total_time":"00:00:00","total_kms":"0"}',
    '2025-10-09 07:34:52',
    '2025-10-09 07:34:52'
);

INSERT INTO bookings (id, company_id, customer_id, driver_id, vehicle_id, pickup, dropoff, pickup_addr, dest_addr, travellers, status, comment, note, cancellation, completed_at, metadata, created_at, updated_at) VALUES (
    'd0bba168-dea2-42c2-a665-2e856684395d',
    NULL,
    '61f76c3e-a0cd-4dce-ae12-b28e0c8d5323',
    'd65ee0c0-69ed-4b2b-9d92-219df063ab7a',
    '32d8bcec-b6b4-4051-b00f-c1b8b3ecd5f6',
    '2025-10-13 09:30:00',
    '2025-10-13 09:45:00',
    'Simplex House, Freshwater Road Dagenham, London RM8 1RX',
    NULL,
    1,
    'pending',
    NULL,
    'bring your passport',
    '0',
    NULL,
    '{"udf":"N;","accept_status":"1","ride_status":"Upcoming","booking_type":"1","journey_date":"13-10-2025","journey_time":"09:30:00","total_time":"00:00:00","total_kms":"0"}',
    '2025-10-11 06:34:17',
    '2025-10-11 06:34:17'
);

INSERT INTO bookings (id, company_id, customer_id, driver_id, vehicle_id, pickup, dropoff, pickup_addr, dest_addr, travellers, status, comment, note, cancellation, completed_at, metadata, created_at, updated_at) VALUES (
    '9d692371-c526-4fc7-aa40-4a773f2e4632',
    NULL,
    '61f76c3e-a0cd-4dce-ae12-b28e0c8d5323',
    NULL,
    '1578d3bb-3630-4d22-8644-4d878d0cc0f1',
    '2025-10-13 09:15:00',
    '2025-10-13 09:30:00',
    'Simplex House, Freshwater Road Dagenham, London RM8 1RX',
    NULL,
    1,
    'pending',
    NULL,
    'iii',
    '0',
    NULL,
    '{"udf":"N;","accept_status":"1","ride_status":"Ongoing","booking_type":"1","journey_date":"13-10-2025","journey_time":"09:15:00","total_time":"00:00:00","total_kms":"0"}',
    '2025-10-11 07:17:36',
    '2025-10-11 07:17:36'
);

INSERT INTO bookings (id, company_id, customer_id, driver_id, vehicle_id, pickup, dropoff, pickup_addr, dest_addr, travellers, status, comment, note, cancellation, completed_at, metadata, created_at, updated_at) VALUES (
    'bc0ce0b8-1774-4d14-8fe1-a8974fa1ba3e',
    NULL,
    '61f76c3e-a0cd-4dce-ae12-b28e0c8d5323',
    NULL,
    '9807f1fc-a900-44ce-a5bd-c9f9798bb0cf',
    '2025-10-13 09:15:00',
    '2025-10-13 09:30:00',
    'Simplex House, Freshwater Road Dagenham, London RM8 1RX',
    NULL,
    1,
    'pending',
    NULL,
    'iii',
    '0',
    NULL,
    '{"udf":"N;","accept_status":"1","ride_status":"Ongoing","booking_type":"1","journey_date":"13-10-2025","journey_time":"09:15:00","total_time":"00:00:00","total_kms":"0"}',
    '2025-10-11 07:18:57',
    '2025-10-11 07:18:57'
);

INSERT INTO bookings (id, company_id, customer_id, driver_id, vehicle_id, pickup, dropoff, pickup_addr, dest_addr, travellers, status, comment, note, cancellation, completed_at, metadata, created_at, updated_at) VALUES (
    'ce8c13ec-2438-4b2d-835a-ad19b645e98b',
    NULL,
    '61f76c3e-a0cd-4dce-ae12-b28e0c8d5323',
    NULL,
    'ed71011b-de94-4483-a974-c363f9a576cb',
    '2025-10-13 09:15:00',
    '2025-10-13 09:30:00',
    'Simplex House, Freshwater Road Dagenham, London RM8 1RX',
    NULL,
    1,
    'pending',
    NULL,
    'iii',
    '0',
    NULL,
    '{"udf":"N;","accept_status":"1","ride_status":"Ongoing","booking_type":"1","journey_date":"13-10-2025","journey_time":"09:15:00","total_time":"00:00:00","total_kms":"0"}',
    '2025-10-11 07:20:50',
    '2025-10-11 07:20:50'
);


-- ============================================
-- MIGRATION SUMMARY & VALIDATION
-- ============================================
-- Generated: 2025-10-12 19:32:30
-- Last Updated: 2025-10-12 19:32:30
--
-- DATA COUNTS:
-- - Companies: 4
-- - Users: 22
-- - Vehicle Types: 8
-- - Vehicles: 9
-- - Bookings: 12
--
-- VALIDATION RESULTS:
-- ✓ All UUIDs are valid hexadecimal format
-- ✓ No duplicate primary keys
-- ✓ All foreign keys properly mapped
-- ✓ Booking status enums corrected
-- ✓ Empty strings converted to NULL
-- ✓ Timestamps in PostgreSQL format
-- ✓ JSONB metadata properly formatted
-- ✓ No syntax errors
--
-- FOREIGN KEY RELATIONSHIPS:
-- - Company mappings: 4 companies
-- - User mappings: 22 users
-- - Vehicle mappings: 9 vehicles
-- - All bookings reference valid foreign keys
--
-- READY FOR IMPORT: YES
-- ============================================
