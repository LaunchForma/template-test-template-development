import { Badge } from '@/shared/components/ui/badge';
import { Card, CardContent } from '@/shared/components/ui/card';
import { type TemplateInfo } from '@/shared/types';
import { ExternalLink } from 'lucide-react';

export function TemplateCard({ template }: { template: TemplateInfo }) {
    return (
        <Card>
            <CardContent className="flex flex-col gap-3 p-4">
                <div className="flex items-start justify-between">
                    <div>
                        <h3 className="font-semibold">{template.name}</h3>
                        {template.entryRoute && (
                            <p className="text-muted-foreground text-sm">
                                {template.entryRoute}
                            </p>
                        )}
                    </div>
                    {template.entryUrl ? (
                        <a
                            href={template.entryUrl}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="bg-primary text-primary-foreground hover:bg-primary/90 inline-flex items-center gap-1.5 rounded-md px-3 py-1.5 text-sm font-medium transition-colors"
                        >
                            Preview
                            <ExternalLink className="size-3.5" />
                        </a>
                    ) : (
                        <button
                            disabled
                            aria-disabled="true"
                            className="text-muted-foreground bg-muted inline-flex cursor-not-allowed items-center gap-1.5 rounded-md px-3 py-1.5 text-sm font-medium opacity-50"
                        >
                            Preview
                            <ExternalLink className="size-3.5" />
                        </button>
                    )}
                </div>
                <div className="flex flex-wrap gap-2">
                    <Badge variant="secondary">
                        {template.routeCount} {template.routeCount === 1 ? 'Route' : 'Routes'}
                    </Badge>
                    <Badge variant="secondary">
                        {template.migrationCount} {template.migrationCount === 1 ? 'Migration' : 'Migrations'}
                    </Badge>
                    <Badge variant="secondary">
                        {template.seederCount} {template.seederCount === 1 ? 'Seeder' : 'Seeders'}
                    </Badge>
                    <Badge variant="secondary">
                        {template.traitCount} {template.traitCount === 1 ? 'Trait' : 'Traits'}
                    </Badge>
                    {template.hasFilamentResources && (
                        <Badge className="border-green-500/30 bg-green-500/10 text-green-600 dark:text-green-400">
                            Filament
                        </Badge>
                    )}
                </div>
            </CardContent>
        </Card>
    );
}
