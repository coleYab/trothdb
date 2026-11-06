export default function Heading({
    title,
    description,
    variant = 'default',
}: {
    title: string;
    description?: string;
    variant?: 'default' | 'small';
}) {
    return (
        <header className={variant === 'small' ? 'space-y-1' : 'mb-10 space-y-2'}>
            <h2
                className={
                    variant === 'small'
                        ? 'display-title text-xl text-foreground sm:text-2xl'
                        : 'display-title text-3xl text-foreground sm:text-4xl'
                }
            >
                {title}
            </h2>
            {description && (
                <p className="max-w-2xl text-sm leading-6 text-muted-foreground">
                    {description}
                </p>
            )}
        </header>
    );
}
