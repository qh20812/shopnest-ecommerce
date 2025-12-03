import React, { forwardRef } from 'react';

type InputElement = HTMLInputElement | HTMLTextAreaElement;

export type InputProps = (
  | (React.InputHTMLAttributes<HTMLInputElement> & { as?: 'input' })
  | (React.TextareaHTMLAttributes<HTMLTextAreaElement> & { as: 'textarea' })
) & { className?: string };

// Reusable Input component that can render input or textarea and merges default styles
const Input = forwardRef<InputElement, InputProps>((props, ref) => {
  const { as = 'input', className = '', ...rest } = props;

  const baseClasses =
    'form-input w-full rounded-lg border-border bg-background px-4 py-2 focus:border-primary focus:ring-primary/50';

  const combined = `${baseClasses} ${className}`.trim();

  if (as === 'textarea') {
    return <textarea ref={ref as React.Ref<HTMLTextAreaElement>} className={combined} {...(rest as React.TextareaHTMLAttributes<HTMLTextAreaElement>)} />;
  }

  return <input ref={ref as React.Ref<HTMLInputElement>} className={combined} {...(rest as React.InputHTMLAttributes<HTMLInputElement>)} />;
});

Input.displayName = 'Input';

export default Input;
