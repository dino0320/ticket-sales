import type { UseFormReturn, FieldValues, Path } from 'react-hook-form'

export function setServerError<TFieldValues extends FieldValues>(errors: Record<string, string>, form: UseFormReturn<TFieldValues>, setOtherErrorMessage: (value: React.SetStateAction<string>) => void) {
    Object.keys(errors).forEach(key => {
        if (Object.keys(form.getValues()).includes(key)) {
            form.setError(key as Path<TFieldValues>, {type: 'server', message: errors[key] ?? ''})
            return
        }
        
        setOtherErrorMessage(errors[key] ?? '')
    })
}
