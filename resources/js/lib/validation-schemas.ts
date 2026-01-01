import { z } from 'zod'

export const nameSchema = z
  .string()
  .min(1, { message: 'Name must be at least 1 characters long' })
  .max(50, { message: 'Name must be at most 50 characters long' })

export const emailSchema = z.email({ message: 'Invalid email address' })

export const passwordSchema = z
  .string()
  .min(8, { message: 'Password must be at least 8 characters long' })
  .max(100, { message: 'Password must be at most 100 characters long' })
  .regex(/^[\p{L}|\p{N}|\p{Z}|\p{S}|\p{P}]+$/u, { message: 'Password must be alphanumeric' })

export const eventTitleSchema = z
  .string()
  .min(1, { message: 'Event title must be at least 1 characters long' })
  .max(100, { message: 'Event title must be at most 100 characters long' })

export const eventDescriptionSchema = z
  .string()
  .min(1, { message: 'Event description must be at least 1 character long' })
  .max(1000, { message: 'Event description must be at most 1000 characters long' })

export const priceSchema = z.coerce.number<number>().min(1, 'Price must be at least 1').max(1000, 'Price must be at most 1000')

export const numberOfTicketsSchema = z.coerce.number<number>().min(1, 'The number of tickets must be at least 1').max(10000, 'The number of tickets must be at most 10000')

export const dateSchema = z.date({message: 'Invalid date'})

export const urlSchema = z.url({ message: 'Invalid URL' })

export const loginFormSchema = z.object({
  email: emailSchema,
  password: passwordSchema,
})

export const registerFormSchema = z
  .object({
    name: nameSchema,
    email: emailSchema,
    password: passwordSchema,
    password_confirmation: z.string(),
  })
  .refine((data) => data.password === data.password_confirmation, {
    path: ['password_confirmation'],
    message: 'Passwords do not match',
  })

export const resetPasswordFormSchema = z
  .object({
    password: passwordSchema,
    new_password: passwordSchema,
    new_password_confirmation: z.string(),
  })
  .refine((data) => data.new_password === data.new_password_confirmation, {
    path: ['new_password_confirmation'],
    message: 'Passwords do not match',
  })

export const applyToBeOrganizerFormSchema = z
  .object({
    event_description: eventDescriptionSchema,
    is_individual: z.boolean(),
    website_url: z.union([
      z.literal(""),
      urlSchema,
    ]),
  })

export const issueTicketFormSchema = z
  .object({
    event_title: nameSchema,
    event_description: z.union([
      z.literal(""),
      eventDescriptionSchema,
    ]),
    price: priceSchema,
    number_of_tickets: numberOfTicketsSchema,
    event_start_date: dateSchema.optional(),
    event_end_date: dateSchema.optional(),
    start_date: dateSchema.optional(),
    end_date: dateSchema.optional(),
  })

export const issueTicketValidationSchema = issueTicketFormSchema.extend({
  event_start_date: dateSchema,
  event_end_date: dateSchema,
  start_date: dateSchema,
  end_date: dateSchema,
})

export const editIssuedTicketFormSchema = z
  .object({
    event_title: nameSchema,
    event_description: z.union([
      z.literal(""),
      eventDescriptionSchema,
    ]),
    number_of_tickets: numberOfTicketsSchema,
    event_start_date: dateSchema,
    event_end_date: dateSchema,
    start_date: dateSchema,
    end_date: dateSchema,
  })
