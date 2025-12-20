import type { UseFormReturn, FieldValues, Path } from 'react-hook-form'
import type { ZodError } from 'zod'

type ErrorRecord = Record<string, string>

export function setManualFormErrors<TFieldValues extends FieldValues>(errors: ErrorRecord, form: UseFormReturn<TFieldValues>, setRootErrorMessage: (value: React.SetStateAction<string>) => void) {
    Object.keys(errors).forEach(key => {
        if (Object.keys(form.getValues()).includes(key)) {
            form.setError(key as Path<TFieldValues>, {type: 'manual', message: errors[key] ?? ''})
            return
        }
        
        setRootErrorMessage(errors[key] ?? '')
    })
}

export function convertZodError(error: ZodError): ErrorRecord {
    const errors: ErrorRecord = {};

    error.issues.forEach((issue) => {
        const name = issue.path.length > 0 ? issue.path.join('.') : 'root'
        errors[name] = issue.message
    })

    return errors
}
