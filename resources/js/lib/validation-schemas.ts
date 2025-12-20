import { z } from 'zod'

export const emailSchema = z.email({ message: 'Invalid email address' })

export const passwordSchema = z
  .string()
  .min(8, { message: 'Password must be at least 8 characters long' })
  .regex(/^[a-zA-Z0-9]+$/, { message: 'Password must be alphanumeric' })

export const nameSchema = z
  .string()
  .min(2, { message: 'Name must be at least 2 characters long' })

export const messageSchema = z
  .string()
  .min(10, { message: 'Message must be at least 10 characters long' })

export const descriptionSchema = z
  .string()
  .min(1, { message: 'Message must be at least 1 character long' })

export const urlSchema = z.url({ message: 'Invalid URL' })

export const dateSchema = z.date({message: 'Invalid date'})

export const contactFormSchema = z.object({
  name: nameSchema,
  email: emailSchema,
  message: messageSchema,
})

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
    email: emailSchema,
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
    event_description: descriptionSchema,
    is_individual: z.boolean(),
    website_url: z.union([
      z.literal(""),
      urlSchema,
    ]),
  })

export const editIssuedTicketFormSchema = z
  .object({
    event_title: nameSchema,
    event_description: z.union([
      z.literal(""),
      descriptionSchema,
    ]),
    number_of_tickets: z.number().min(1, 'The number of tickets must be at least 1 character long'),
    event_start_date: dateSchema,
    event_end_date: dateSchema.nullable(),
    start_date: dateSchema,
    end_date: dateSchema,
  })
