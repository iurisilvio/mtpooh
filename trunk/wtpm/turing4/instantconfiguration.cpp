#include "machine.h"
#include <cstdlib>

int numdigits(int num)
{
    int count = 0;
    while (num > 0)
    {
        ++count;
        num /= 10;
    }

    return count;
}

void instantconfiguration::print(int stepIndex) const
{
    const char *beforeNextRead = a1.c_str();
    char nextRead = (a2.size() > 0) ? a2[0] : '#';
    const char *afterNextRead = (a2.size() > 1) ? (a2.c_str() + 1) : "";

    printf("%d) %s(%c)%s\n", stepIndex, beforeNextRead, nextRead, afterNextRead);

    for (int i = -(2 + numdigits(stepIndex)); i <= (signed)a1.size(); ++i) putc(' ', stdout);
    printf("%s\n\n", s->name.c_str());
}

int sign(int num)
{
    if (num > 0) return 1;
    else if (num < 0) return - 1;
    else return 0;
}

bool instantconfiguration::operator < (const instantconfiguration& b) const
{
    const instantconfiguration& a = (*this);

    int comparison = 4*sign(a.a1.compare(b.a1)) + 2*sign(a.a2.compare(b.a2)) + sign(a.s - b.s);
    return (comparison > 0);
}
