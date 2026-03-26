import { TemplateCard } from '@/shared/components/dev/template-card';
import AppLayout from '@/shared/layouts/app-layout';
import { type BreadcrumbItem, type TemplateInfo } from '@/shared/types';
import { templates as devTemplates } from '@/routes/dev';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dev Tools' },
    { title: 'Templates', href: devTemplates().url },
];

export default function Templates({ templates }: { templates: TemplateInfo[] }) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Templates" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <div>
                    <h2 className="text-xl font-semibold">Templates</h2>
                    <p className="text-muted-foreground text-sm">
                        Registered templates in this monorepo
                    </p>
                </div>
                <div className="flex flex-col gap-3">
                    {templates.map((template) => (
                        <TemplateCard key={template.slug} template={template} />
                    ))}
                </div>
            </div>
        </AppLayout>
    );
}
